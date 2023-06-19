<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Component;
use App\Models\File;


class FileUpload extends Component
{
    use WithFileUploads;

    public $input;

    public function save()
    {
        $this->validate([
            'input' => 'required|file|mimetypes:video/mp4,audio/mpeg',
        ]);

        
        $this->fileName = $this->input->getClientOriginalName();
        $this->extension = $this->input->getClientOriginalExtension();

        if($this->extension == 'mp4'){
            $this->storePath = 'public/media/videos/';
            $this->accessPath = 'storage/media/videos/' . $this->fileName;
            $this->url = 'http://127.0.0.1:8080/media/videos/' . $this->fileName;
            $this->type = 'video';
        }else{
            $this->storePath = 'public/media/audios/';
            $this->accessPath = 'storage/media/audios/' . $this->fileName;
            $this->url = 'http://127.0.0.1:8080/media/audios/' . $this->fileName;
            $this->type = 'audio';
        }
        
        $this->isFileUploaded = $this->input->storeAs($this->storePath, $this->fileName);

        if ($this->isFileUploaded) {
            $this->file = new File();
            $this->file->path = $this->accessPath;
            $this->file->type = $this->type;
            $this->file->url = $this->url;
            $this->file->title = $this->fileName;
            $this->file->user_id = Auth::id();;
            $this->file->save();
            $this->emit('fileUploaded');
        }
    }
}
