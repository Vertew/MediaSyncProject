<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource belonging to a specific user.
     */
    public function index_user()
    {
        $id = Auth::id();
        $videos = Video::where('user_id', $id)->get()->sortByDesc('created_at');
        return view('videos.index', ['videos' => $videos]);
    }

    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'video' => 'required|file|mimetypes:video/mp4',
        ]);

        $fileName = $request->video->getClientOriginalName();
        $storePath = 'videos/' . $fileName;
        $accessPath = 'storage/videos/' . $fileName;

        $isFileUploaded = Storage::disk('public')->put($storePath, file_get_contents($request->video));
        $url = Storage::disk('public')->url($accessPath);

        if ($isFileUploaded) {
            $video = new Video();
            $video->path = $accessPath;
            $video->title = $fileName;
            $video->user_id = Auth::id();;
            $video->save();
 
            return back()
            ->with('success','Video has been successfully uploaded.');
        }

        return back()
            ->with('error','Unexpected error occured');

    }

    /**
     * Display the specified resource.
     */

    public function show(string $id = '1')
    {
        $video = Video::findOrFail($id);
        return view('videos.show', ['video' => $video]);
    }


    public function room()
    {
        return view('videos.room');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
