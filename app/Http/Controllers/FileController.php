<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $extension= $request->file->getClientOriginalExtension();

        if($extension == 'mp4'){
            $storePath = 'media/videos/' . $fileName;
            $accessPath = 'storage/media/videos/' . $fileName;
            $type = 'video';
        }else{
            $storePath = 'media/audios/' . $fileName;
            $accessPath = 'storage/media/audios/' . $fileName;
            $type = 'audio';
        }
        

        $isFileUploaded = Storage::disk('public')->put($storePath, file_get_contents($request->file));
        $url = Storage::disk('public')->url($accessPath);

        if ($isFileUploaded) {
            $file = new File();
            $file->path = $accessPath;
            $file->type = $type;
            $file->title = $fileName;
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
