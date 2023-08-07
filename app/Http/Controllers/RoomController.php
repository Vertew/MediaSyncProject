<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Events\RoomDeletedEvent;
use App\Events\UpdateQueueEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Room;
use App\Models\File;
use App\Models\Role;

class RoomController extends Controller
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
        if(Gate::allows('full-account') || Auth::user()->rooms->count() == 0){
            return view('rooms.create');
        }else{
            session()->flash('message', 'Guest users can only have one room at a time.');
            session()->flash('alert-class', 'alert-warning');
            return redirect()->route('home');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:15',
        ]);

        $room = new Room;
        $room->user_id = Auth::id();
        $room->name = $validatedData['name'];
        $room->key = Str::random(16);
        $room->save();

        $roles = Role::Get();
        $role = $roles->firstWhere('role', 'Admin'); // The user who created the room is automatically an admin

        $user = Auth::user();
        $user->roles()->attach($role, ['room_id' => $room->id]);


        session()->flash('message', 'New room created.');
        return redirect()->route('rooms.show', ['key' => $room->key]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $key)
    {
        $room = Room::where('key',$key)->firstOrFail();

        if(Auth::user()->banned_from->contains($room)){
            session()->flash('message', 'You are banned from this room.');
            session()->flash('alert-class', 'alert-danger');
            return redirect()->route('home');
        }elseif($room->locked){
            session()->flash('message', 'This room is currently locked.');
            session()->flash('alert-class', 'alert-warning');
            return redirect()->route('home');
        }else{
            return view('rooms.show', ['room' => $room]);
        }
    }

    public function updateQueue(Request $request){
        $room = Room::findOrFail($request->room_id);
        $file = File::findOrFail($request->file);
        $room->files()->attach($file);
        UpdateQueueEvent::dispatch(auth()->user(), $request->room_id);
        return null;
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
        RoomDeletedEvent::dispatch($id);
        $room = Room::findOrFail($id);
        $room->delete();
        session()->flash('message', 'Room deleted.');
        return redirect()->route('home');
    }
}
