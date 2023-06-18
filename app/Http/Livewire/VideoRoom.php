<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\File;
use App\Models\User;

class VideoRoom extends Component
{

    public $current_file = "empty";
    public $title_vid = "Video player empty...";
    public $title_snd = "Audio player empty...";
    public $slctd_title_vid = "";
    public $slctd_title_snd = "";

    public function mount()
    {
        // Initialising videos
        $this->videos = Auth::user()->files->where('type', 'video');
        $this->audios = Auth::user()->files->where('type', 'audio');
    }

    public function set_media(File $file){
        $this->current_file = $file->url;
        if($file->type == "video"){
            $this->slctd_title_vid  = $file->title;
        }else{
            $this->slctd_title_snd = $file->title;
        }
    }

    public function set_title(String $title, String $type){
        if($type == "video"){
            $this->title_vid = $title;
        }else{
            $this->title_snd = $title;
        }
    }
    
    public function render()
    {
        return view('livewire.video-room', ['videos' => $this->videos->sortByDesc('created_at')], ['audios' => $this->audios->sortByDesc('created_at')]);
    }
}
