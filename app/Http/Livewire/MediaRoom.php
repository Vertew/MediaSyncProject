<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\File;
use App\Models\User;

class MediaRoom extends Component
{

    public int $current_file;
    public $title = "Media player empty...";
    public $slctd_id;
    public $slctd_title = "";
    public $audio_slctd = false;
    public $video_slctd = false;
    public $room;

    protected $listeners = ['fileUploaded' => '$refresh'];

    public function mount()
    {
        // Initialising media files
        $this->videos = Auth::user()->files->where('type', 'video');
        $this->audios = Auth::user()->files->where('type', 'audio');
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
                                           ['room' => $this->room]);
    }
}
