<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Video;
use App\Models\User;

class VideoIndex extends Component
{

    public $current_video = "empty";

    public function mount()
    {
        // Initialising videos
        $this->videos = Auth::user()->videos;
    }
    
    public function render()
    {
        return view('livewire.video-index', ['videos' => $this->videos->sortByDesc('created_at')]);
    }
}
