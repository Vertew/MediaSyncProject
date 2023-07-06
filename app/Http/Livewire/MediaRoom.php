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
    public $title = "Media player empty...";
    public $slctd_id;
    public $slctd_title = "";
    public $audio_slctd = false;
    public $video_slctd = false;
    public string $queue_mode = "sequential";
    public $room;
    public $queue;

    public function mount()
    {
        // Initialising media files
        $this->videos = Auth::user()->files->where('type', 'video');
        $this->audios = Auth::user()->files->where('type', 'audio');
        $this->queue = $this->room->files->sortBy('pivot.created_at');
    }

    public function dump(){
        dd($this->queue);
    }

    public function getListeners()
    {
        return [
            "echo-presence:presence.chat.{$this->room->id},.update-queue" => 'updateQueue',
            "echo-presence:presence.chat.{$this->room->id},.change-mode" => 'changeMode',
            'fileUploaded' => '$refresh'
        ];
    }

    public function updateQueue(){
        $this->room->refresh();
        $this->queue = $this->room->files->sortBy('pivot.created_at');
    }

    public function changeMode(array $event){
        $this->queue_mode = $event["newMode"];
    }

    public function broadcastMode(string $newMode){
        ChangeModeEvent::dispatch(Auth::user(), $newMode, $this->room->id);
    }

    public function removeFromQueue(int $file_id){
        $this->room->files()->detach($file_id);
        UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
    }

    public function playNext(){
        $this->file = $this->queue->first();
        if ($this->file != null){
            $this->room->files()->detach($this->file->id);
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
