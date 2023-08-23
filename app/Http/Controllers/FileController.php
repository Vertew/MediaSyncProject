<?php

namespace App\Http\Controllers;
require '../vendor/autoload.php';

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;
use FFMpeg\FFMpeg;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimetypes:video/mp4,audio/mpeg',
        ]);

        $fileName = $request->file->getClientOriginalName();
        $extension = $request->file->getClientOriginalExtension();

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
        

        $isFileUploaded = Storage::disk('public')->put($storePath, file_get_contents($request->file));
        //$url = Storage::disk('public')->url($accessPath);

        if ($isFileUploaded) {

            $ffprobe = FFProbe::create();
            $ffmpeg = FFMpeg::create();

            if($type == 'video'){  
                $thumbnail = 'storage/media/videos/' . $username .'/'. Str::of($fileName)->basename('.'.$extension).'.jpg';
                
                $duration = $ffprobe->format($accessPath)->get('duration');

                $video = $ffmpeg->open($accessPath);
                $video->frame(TimeCode::fromSeconds(floor($duration/2)))->addFilter(new CustomFrameFilter('scale=1920x938'))->save($thumbnail);
            }else{
                $thumbnail = 'storage/media/audios/audio_icon.png';
            }

            $file = new File();
            $file->path = $accessPath;
            $file->type = $type;
            $file->url = $url;
            $file->title = $fileName;
            $file->original_title = $originalName;
            $file->thumbnail = $thumbnail;
            $file->user_id = Auth::id();;
            $file->save();
 
            return back()
            ->with('success','Video has been successfully uploaded.');
        }

        return back()
            ->with('error','Unexpected error occured');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
