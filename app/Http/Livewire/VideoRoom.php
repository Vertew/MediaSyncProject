<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Video;
use App\Models\User;

class VideoRoom extends Component
{

    public $current_video = "empty";
    public $video_title = "No video selected yet...";

    public function mount()
    {
        // Initialising videos
        $this->videos = Auth::user()->videos;
    }

    public function set_media(Video $video){
        $this->current_video = asset($video->path);
        $this->video_title = $video->title;
    }
    
    public function render()
    {
        return view('livewire.video-room', ['videos' => $this->videos->sortByDesc('created_at')]);
    }
}
