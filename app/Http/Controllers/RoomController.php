<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Room;

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
        return view('rooms.create');
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

        session()->flash('message', 'New room created.');
        return redirect()->route('rooms.show', ['key' => $room->key]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $key)
    {
        $room = Room::where('key',$key)->firstOrFail();
        return view('rooms.show', ['room' => $room]);
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
