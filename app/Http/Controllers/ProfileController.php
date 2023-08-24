<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $profile = Profile::findOrFail($id);
        return view('profiles.show', ['profile' => $profile]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $profile = Profile::findOrFail($id);
        if(Gate::allows('private', $profile->user->id)){
            return view('profiles.edit', ['profile' => $profile]);
        }else{
            session()->flash('message', 'You do not have permission to edit this profile.');
            session()->flash('alert-class', 'alert-danger');
            return redirect()->route('profiles.show', ['id'=> $id]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|max:30',
            'date_of_birth' => 'nullable|date',
            'status' => 'nullable|max:100',
            'location' => 'nullable|max:30',
            'picture' => 'nullable|image',
        ]);

        $profile = Profile::findOrFail($id);
        $user = User::findOrFail($profile->user->id);

        $default = "storage/media/images/DefaultProfileIcon.png";

        $profile->name = $validatedData['name'];
        $profile->date_of_birth = $validatedData['date_of_birth'];
        $profile->status = $validatedData['status'];
        $profile->location = $validatedData['location'];

        if($request->picture != null){

            if($user->picture != $default){
                SystemFile::delete($user->picture);
            }

            $fileName = $request->picture->getClientOriginalName();

            $storePath = 'public/media/images/'. $user->username;
            $isImageUploaded = $request->picture->storeAs($storePath, $fileName);

            if($isImageUploaded){

                $user->picture = 'storage/media/images/' . $user->username .'/'. $fileName;
            }
        }
        if($request['checkbox'] && $user->picture != $default){
            SystemFile::delete($user->picture);
            $user->picture = $default;
        }

        $profile->save();
        $user->save();

        session()->flash('message', 'Profile was updated.');
        return redirect()->route('profiles.show', ['id'=> $profile->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
