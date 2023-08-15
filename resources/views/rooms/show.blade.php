@extends('layouts.app')

@section('title')

@section('head')

@vite('resources/js/app.js')
@vite('resources/css/room.css')

@endsection

@section('content')

<script>
    // Setting some values in JS
    const currentUser = {{ Js::from(Auth::user()->username) }};
    const currentUserId = {{ Js::from(Auth::user()->id) }};
    const currentRoom = {{ Js::from($room->id) }};
    var myFriends = {{ Js::from(Auth::user()->friends->pluck('username')) }};
</script>

<div class = "container-fluid mt-3 text-center">

    <div class ="d-flex align-items-end justify-content-center">
        <h1 class='display-2'>{{$room->name}}</h1>
        <h1 class='display-5'><span id="title" class="badge {{$room->locked ? "bg-danger" : "bg-success"}} rounded-pill mx-1">{{$room->locked ? "Locked" : "Open"}}</span></h1>
    </div>

    <livewire:media-room :room="$room" :queue="$room->files">

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