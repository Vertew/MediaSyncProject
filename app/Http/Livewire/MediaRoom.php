<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Support\Facades\Auth;
use App\Events\UpdateQueueEvent;
use App\Events\ChangeModeEvent;
use App\Events\SetEvent;
use Livewire\Component;
use App\Models\File;
use App\Models\User;
use App\Models\Room;


class MediaRoom extends Component
{

    public int $current_file;
    public int $active_file_id;
    public array $currentUsers = [];
    public $title = "Media player empty...";
    public $slctd_id;
    public $slctd_title = "";
    public $audio_slctd = false;
    public $video_slctd = false;
    public string $queue_mode = "sequential";
    public File $myVote;
    public array $shuffle_array = [];
    public $room;
    public $queue;

    public function mount()
    {
        // Initialising
        $this->videos = Auth::user()->files->where('type', 'video');
        $this->audios = Auth::user()->files->where('type', 'audio');
        $this->queue = $this->room->files->sortBy('pivot.created_at');
        $i=1;
        foreach ($this->queue as $file) {
            $this->shuffle_array[$i] = ($this->room->files->find($file->id))->pivot->votes;
            $i++;
        }
        $this->myVote = new File;
        MediaRoom::resetVotes();
    }

    public function getListeners()
    {
        return [
            "echo-presence:presence.chat.{$this->room->id},.update-queue" => 'updateQueue',
            "echo-presence:presence.chat.{$this->room->id},.change-mode" => 'changeMode',
            "echo-presence:presence.chat.{$this->room->id},.media-set" => 'updateValues',
            "echo-presence:presence.chat.{$this->room->id},joining" => 'joining',
            "echo-presence:presence.chat.{$this->room->id},leaving" => 'leaving',
            "echo-presence:presence.chat.{$this->room->id},here" => 'here',
            'fileUploaded' => '$refresh'
        ];
    }

    public function here(array $users) {
        $this->currentUsers = $users;
    }

    public function joining(array $user) {
        //MediaRoom::resetVotes();
        $this->currentUsers[] = $user;
        ChangeModeEvent::dispatch(Auth::user(), $this->queue_mode, $this->room->id, $this->shuffle_array);
    }

    public function leaving(array $user) {
        //MediaRoom::resetVotes();
        if(($key = array_search($user, $this->currentUsers)) !== false) {
            unset($this->currentUsers[$key]);
        }
        ChangeModeEvent::dispatch(Auth::user(), $this->queue_mode, $this->room->id, $this->shuffle_array);
    }

    public function updateValues(array $event) {
        $this->active_file_id = $event["file"]["id"];
    }

    public function dump(){
        dd($this->currentUsers);
    }

    public function placeVote(File $file){
        if ($this->myVote->id != null){
            if($this->myVote != $file){
                $this->room->files()->updateExistingPivot($file->id, ['votes' => $this->room->files->find($file->id)->pivot->votes += 1]);
                $this->room->files()->updateExistingPivot($this->myVote->id, ['votes' => $this->room->files->find($this->myVote->id)->pivot->votes -= 1]);
                $this->myVote = $file;
                UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
            }  
        }else{
            $this->room->files()->updateExistingPivot($file->id, ['votes' => $this->room->files->find($file->id)->pivot->votes += 1]);
            $this->myVote = $file;
            UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
        } 
    }

    public function resetVotes() {
        foreach ($this->queue as $file) {
            $this->room->files()->updateExistingPivot($file->id, ['votes' => $this->room->files->find($file->id)->pivot->votes = 0]);
        }
        $this->myVote = new File;
        UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
    }

    public function updateQueue(){
        $this->room->refresh();
        if($this->queue_mode == "sequential")
            $this->queue = $this->room->files->sortBy('pivot.created_at');
        else if($this->queue_mode == "vote"){
            $this->queue = $this->room->files->sortByDesc('pivot.votes');
        }else if($this->queue_mode == "random"){
            $i = 1;
            foreach($this->room->files as $file){
                $this->room->files()->updateExistingPivot($file->id, ['votes' => $this->room->files->find($file->id)->pivot->votes = $this->shuffle_array[$i]]);
                $i++;
            }
            $this->queue = $this->room->files->sortByDesc('pivot.votes');
        }
    }

    public function changeMode(array $event){
        $this->queue_mode = $event["newMode"];
        $this->shuffle_array = $event["shuffle_array"];
        MediaRoom::resetVotes();
        UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
    }

    public function broadcastMode(string $newMode){
        if ($newMode == "random"){
            for ($i = 1; $i <= count($this->room->files); $i++) {
                $this->shuffle_array[$i] = rand(1,count($this->room->files));
            }
        }
        ChangeModeEvent::dispatch(Auth::user(), $newMode, $this->room->id, $this->shuffle_array);
    }

    public function removeFromQueue(int $file_id){
        $this->room->files()->detach($file_id);
        MediaRoom::resetVotes();
        UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
    }

    public function playNext(){
        $this->file = $this->queue->first();
        if ($this->file != null){
            $this->room->files()->detach($this->file->id);
            MediaRoom::resetVotes();
            UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
            SetEvent::dispatch(Auth::user(), $this->file->id, $this->room->id);
        }
    }

    public function set_media(File $file){
        //$this->current_file = $file->url;
        $this->current_file = $file->id;
        $this->slctd_title = $file->title;
        if($file->type == "video"){
            $this->video_slctd = true;
            $this->audio_slctd = false;
        }else {
            $this->video_slctd = false;
            $this->audio_slctd = true;
        }
    }

    public function set_title(String $title, String $type){
        $this->title = $title;
    }

    public function delete($fileid)
    {
        if ($fileid != -1){
            $this->file = File::findOrFail($fileid);
            SystemFile::delete('storage/media/'.$this->file->type.'s/'.$this->file->title);
            $this->file->delete();
            session()->flash('message', 'File deleted.');
            $this->emit('file-deleted');

            if($fileid == $this->active_file_id){
                // If the current file in the player is deleted then set the file
                // to be the default file in the player.
                SetEvent::dispatch(Auth::user(), 1, $this->room->id);
            }
        }else{
            session()->flash('message', 'Select a file to delete');
        }
    }
    
    public function render()
    {
        $this->videos = Auth::user()->files->where('type', 'video');
        $this->audios = Auth::user()->files->where('type', 'audio');
        return view('livewire.media-room', ['videos' => $this->videos->sortByDesc('created_at')], 
                                           ['audios' => $this->audios->sortByDesc('created_at')], 
                                           ['room' => $this->room],
                                           ['queue' => $this->queue]);
    }
}
