@extends('layouts.app')

@section('title')

@section('head')

@vite('resources/js/app.js')
@vite('resources/css/room.css')

@endsection

@section('content')

<script>
    // Passing some important values to JS
    const currentUser = {{ Js::from(Auth::user()->username) }};
    const currentUserId = {{ Js::from(Auth::user()->id) }};
    const currentRoom = {{ Js::from($room->id) }};
    var myFriends = {{ Js::from(Auth::user()->friends->pluck('username')) }};
</script>

<div class = "container-fluid mt-3 text-center">

    <h1 class='display-2' id="title">{{$room->name}} 🔓</h1>

    <livewire:media-room :room="$room" :queue="$room->files">

    <div class = "container-md mt-5 text-center">
        <button class="btn btn-primary" onclick="showhide('upload-div')"> Upload media</button>
    </div>
    
    <div class = "container-md mt-3 text-center" id = "upload-div" style="display: none">
        <livewire:file-upload />
    </div>

    @if(Auth::user()->roles->where('role', 'Admin')->contains('pivot.room_id', $room->id))
        <div class = "container-md mt-5 text-center">
            <form method="POST" action="{{ route('rooms.destroy', ['id'=> $room->id])}}">
                @csrf
                @method('DELETE')
                <input class="btn btn-danger" type = "submit" value = "Delete Room" onclick="return confirm('Are you sure?')">
            </form>
        </div>
    @endif

    <script>
        function showhide(id) {
            var div = document.getElementById(id);
            if (div.style.display === "none") {
                div.style.display = "block";
            } else {
                div.style.display = "none";
             }
        }
        function reset(id){
            var element = document.getElementById(id).reset();
        }

    </script>
</div>

<div class="mt-5 p-4 bg-dark text-white text-center"> <p>MediaSync - Copyright 2023 - Sam Tudberry - 1907632</p> </div>

@endsection