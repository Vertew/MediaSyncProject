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
    public $title_vid = "Video player empty...";
    public $title_snd = "Audio player empty...";
    public $slctd_id_vid;
    public $slctd_id_snd;
    public $slctd_title_vid = "";
    public $slctd_title_snd = "";
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
        $this->slctd_title_vid  = $file->title;
        $this->slctd_id_vid = $file->id;
    }

    public function set_title(String $title, String $type){
        $this->title_vid = $title;
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
