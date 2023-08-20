<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Notifications\FriendRequest;
use App\Events\RequestRecievedEvent;
use App\Events\UserUnbannedEvent;
use App\Events\UpdateQueueEvent;
use App\Events\LockToggledEvent;
use App\Events\RoleChangedEvent;
use App\Events\UserBannedEvent;
use App\Events\ChangeModeEvent;
use Livewire\WithFileUploads;
use App\Events\KickUserEvent;
use Illuminate\Support\Str;
use App\Events\SetEvent;
use Livewire\Component;
use App\Models\File;
use App\Models\User;
use App\Models\Room;
use App\Models\Role;


class MediaRoom extends Component
{
    use WithFileUploads;

    public int $current_file;
    public int $active_file_id = -1;
    public Collection $userCollection;
    public $title = "Media player empty...";
    public $slctd_id;
    public $slctd_title = "";
    public $audio_slctd = false;
    public $video_slctd = false;
    public $moderator_level;
    public $standard_level;
    public string $queue_mode = "sequential";
    public File $myVote;
    public array $shuffle_array = [];
    public $roles;
    public $room;
    public $queue;
    public $user;
    public $input;

    public function mount()
    {
        // Initialising

        $this->user = Auth::user();
        $this->userCollection = new Collection();

        $this->videos = $this->user->files->where('type', 'video');
        $this->audios = $this->user->files->where('type', 'audio');
        $this->queue = $this->room->files->sortBy('pivot.created_at');
        $this->roles = Role::Get();
        $i=1;
        foreach ($this->queue as $file) {
            $this->shuffle_array[$i] = ($this->room->files->find($file->id))->pivot->votes;
            $i++;
        }
        $this->myVote = new File;
        MediaRoom::resetVotes();

        if($this->user->roles->where('pivot.room_id', $this->room->id)->isEmpty()){
            $role = $this->roles->firstWhere('role', 'Standard');
            $this->user->roles()->attach($role, ['room_id' => $this->room->id]);
        }
    }

    public function getListeners()
    {
        return [
            "echo-presence:presence.chat.{$this->room->id},.update-queue" => 'updateQueue',
            "echo-presence:presence.chat.{$this->room->id},.change-mode" => 'changeMode',
            "echo-presence:presence.chat.{$this->room->id},.media-set" => 'updateValues',
            "echo-presence:presence.chat.{$this->room->id},.kick-user" => 'kicked',
            "echo-presence:presence.chat.{$this->room->id},.user-banned" => 'banned',
            "echo-presence:presence.chat.{$this->room->id},joining" => 'joining',
            "echo-presence:presence.chat.{$this->room->id},leaving" => 'leaving',
            "echo-presence:presence.chat.{$this->room->id},here" => 'here',
            'fileUploaded' => 'resestInput',
            'roleChanged' => '$refresh'
        ];
    }

    public function here(array $event) {
        foreach($event as $user){
            $this->userCollection->push(User::find($user['id']));
        }
        $user = Auth::user();
        RoleChangedEvent::dispatch($user, $this->room->id, $user->roles->firstWhere('pivot.room_id', $this->room->id));
    }

    public function joining(array $event) {
        //MediaRoom::resetVotes();
        $this->userCollection->push(User::find($event['id']));
        ChangeModeEvent::dispatch(Auth::user(), $this->queue_mode, $this->room->id, $this->shuffle_array);
    }

    public function leaving(array $event) {
        //MediaRoom::resetVotes();
        $this->userCollection = $this->userCollection->whereNotIn('id', $event['id']);
        ChangeModeEvent::dispatch(Auth::user(), $this->queue_mode, $this->room->id, $this->shuffle_array);
    }

    // We reset the input value when a file is uploaded so the user isn't able to keep 
    // uploading the same file despite the form appearing empty.
    public function resestInput() {
        $this->input = null;
    }

    public function toggleLock(){
        if (Gate::allows('admin-action', $this->room->id)) {
            $this->room->refresh();
            if($this->room->locked == 1){
                $this->room->locked = 0;
                $this->room->save();
                LockToggledEvent::dispatch(Auth::user(), false, $this->room->id);
            }else{
                $this->room->locked = 1;
                $this->room->save();
                LockToggledEvent::dispatch(Auth::user(), true, $this->room->id);
            }
        }
    }

    public function ban(int $victim_id) {
        // Only admins can ban, can't ban self, can't ban room owner
        if (Gate::allows('admin-action', $this->room->id) && $victim_id != Auth::id() && $victim_id != $this->room->user->id) {
            $victim = User::find($victim_id);
            $victim->banned_from()->attach($this->room);
            UserBannedEvent::dispatch(Auth::user(), $victim, $this->room->id);
        }
    }

    public function banned(array $event) {
        if (Auth::user()->id == $event["victim"]["id"]){
            session()->flash('message', 'You have been banned from '.$this->room->name.' by '.$event['user']['username'].'.');
            session()->flash('alert-class', 'alert-danger');
            return redirect()->route('home');
        }
    }

    public function unban(User $user) {
        if (Gate::allows('admin-action', $this->room->id)) {
            $user->banned_from()->detach($this->room);
            $this->room->refresh();
            UserUnbannedEvent::dispatch(Auth::user(), $user, $this->room);
        }
    }

    public function kick(int $victim_id) {
        $victim = User::find($victim_id);

        // Admins can kick other admins but mods can't kick admins
        if (Gate::allows('admin-action', $this->room->id)) {
            KickUserEvent::dispatch(Auth::user(), $victim, $this->room->id);
        }elseif (Gate::allows('moderator-action', $this->room->id)){
            if($victim->roles->firstWhere('pivot.room_id', $this->room->id)->id != 1) {
                KickUserEvent::dispatch(Auth::user(), $victim, $this->room->id);
            }
        }
    }

    public function kicked(array $event) {
        if (Auth::user()->id == $event["victim"]["id"]){
            session()->flash('message', 'You were kicked from '.$this->room->name.' by '.$event['user']['username'].'.');
            session()->flash('alert-class', 'alert-warning');
            return redirect()->route('home');
        }
    }

    public function toggleRole(int $newRole, int $userId) {

        // Make sure user is allowed to change roles (owner's roles cannot be changed from admin)
        if (Gate::allows('admin-action', $this->room->id) && ($userId != $this->room->user->id)) {
            $user = User::find($userId);
            // Check if user already as the new role
            if(!($user->roles->where('pivot.room_id', $this->room->id)->first()?->id == $newRole)){

                // Detach current roles associated with user through room
                $user->roles()->wherePivot('room_id', $this->room->id)->detach();

                // Attach new role
                $role = $this->roles->find($newRole);
                $user->roles()->attach($role, ['room_id' => $this->room->id]);

                RoleChangedEvent::dispatch($user, $this->room->id, $role);
                $this->emitSelf('roleChanged');
            }   
        }
    }

    public function updateValues(array $event) {
        $this->active_file_id = $event["file"]["id"];
    }

    public function dump(){
        //Auth::user()->friends()->attach(User::find(2));
        //User::find(2)->friends()->attach(Auth::user());
        //Auth::user()->banned_from()->attach($this->room);
        dd($this->input);
    }

    public function sendRequest(int $recipient_id) {
        $recipient =  User::find($recipient_id);

        // Only sends a request if the recipient doesn't already have one from the same source.
        if(is_null($recipient->notifications()->firstWhere('data->sender_id', Auth::user()->id)) && $recipient->guest == false){
            $recipient->notify(new FriendRequest(Auth::user()));
            RequestRecievedEvent::dispatch($recipient->id, $this->user);
        }
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
        if (Gate::allows('moderator-action', $this->room->id)) {
            if ($newMode == "random"){
                for ($i = 1; $i <= count($this->room->files); $i++) {
                    $this->shuffle_array[$i] = rand(1,count($this->room->files));
                }
            }
            ChangeModeEvent::dispatch(Auth::user(), $newMode, $this->room->id, $this->shuffle_array);
        }
    }

    public function removeFromQueue(int $file_id){
        if (Gate::allows('moderator-action', $this->room->id)) {
            $this->room->files()->detach($file_id);
            MediaRoom::resetVotes();
            UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
        }
    }

    public function playNext(){
        if(Gate::allows('standard-action', $this->room->id)) {
            $this->file = $this->queue->first();
            if ($this->file != null){
                $this->room->files()->detach($this->file->id);
                $this->room->file_id = $this->file->id;
                $this->room->save();
                MediaRoom::resetVotes();
                UpdateQueueEvent::dispatch(Auth::user(), $this->room->id);
                SetEvent::dispatch(Auth::user(), $this->file->id, $this->room->id);
            }
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

    public function save()
    {
        $this->validate([
            'input' => 'required|file|mimetypes:video/mp4,audio/mpeg',
        ]);
        
        $fileName = $this->input->getClientOriginalName();
        $extension = $this->input->getClientOriginalExtension();

        $originalName = $fileName;

        $username = Auth::user()->username;

        $title = Str::of($fileName)->basename('.'.$extension);

        // Files with the same name uploaded by the same user need to have their filenames modified to avoid conflicts.
        // This also accounts for situations where a file with the new modified filename as it's original name already exists,
        // as unlikely an event as that might be.
        $i = 0;
        while(Auth::user()->files->contains('title',$fileName)){
            $fileName = $title.'-'.Auth::user()->files->where('original_title', $fileName)->count()+$i.'.'.$extension;
            $i++;
        }

        if($extension == 'mp4'){
            $storePath = 'public/media/videos/'. $username;
            $accessPath = 'storage/media/videos/' . $username .'/'. $fileName;
            $url = 'http://127.0.0.1:8080/media/videos/' . $username .'/' . $fileName;
            $type = 'video';
        }else{
            $storePath = 'public/media/audios/'. $username;
            $accessPath = 'storage/media/audios/' . $username .'/'. $fileName;
            $url = 'http://127.0.0.1:8080/media/audios/' . $username .'/' . $fileName;
            $type = 'audio';
        }
        
        $isFileUploaded = $this->input->storeAs($storePath, $fileName);

        if ($isFileUploaded) {
            $file = new File();
            $file->path = $accessPath;
            $file->type = $type;
            $file->url = $url;
            $file->title = $fileName;
            $file->original_title = $originalName;
            $file->user_id = Auth::id();;
            $file->save();
            $this->emitSelf('fileUploaded');
        }

    }

    public function delete($fileid)
    {
        if ($fileid != -1){
            if($fileid == $this->active_file_id){
                // If the current file in the player is deleted then set the default file
                // to be the file in the player.
                SetEvent::dispatch(Auth::user(), 1, $this->room->id);
                $this->room->file_id = 1;
                $this->room->save();
            }

            $file = File::findOrFail($fileid);
            SystemFile::delete($file->path);
            $file->delete();
            session()->flash('message', 'File deleted.');
            $this->emit('file-deleted');

            
        }else{
            session()->flash('message', 'Select a file to delete');
        }
    }
    
    public function render()
    {
        $this->moderator_level = Gate::allows('moderator-action', $this->room->id);
        $this->standard_level = Gate::allows('standard-action', $this->room->id);
        $this->videos = Auth::user()->files->where('type', 'video');
        $this->audios = Auth::user()->files->where('type', 'audio');
        $this->user = Auth::user();
        
        return view('livewire.media-room', ['videos' => $this->videos->sortByDesc('created_at')], 
                                           ['audios' => $this->audios->sortByDesc('created_at')], 
                                           ['room' => $this->room],
                                           ['queue' => $this->queue]);
    }
}
