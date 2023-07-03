@extends('layouts.app')

@section('title')

@section('head')

@vite('resources/js/app.js')

@endsection

@section('content')

<script>
    // Getting the current user on the page
    const currentUser = {{ Js::from(Auth::user()->username) }};
    const currentRoom = {{ Js::from($room->id) }}
</script>

<div class = "container-fluid mt-3">

    <h1 class='display-5 text-center'>{{$room->name}}</h1>

    <livewire:media-room :room="$room">

    <div class = "container-md mt-5 text-center">
        <button class="btn btn-primary" onclick="showhide('upload-div')"> Upload media</button>
    </div>
    

    <div class = "container-md mt-3 text-center" id = "upload-div" style="display: none">
        <livewire:file-upload />
    </div>

    <div class = "container-md mt-5 text-center">
        <form method="POST" action="{{ route('rooms.destroy', ['id'=> $room->id])}}">
            @csrf
            @method('DELETE')
            <input class="btn btn-danger" type = "submit" value = "Delete Room" onclick="return confirm('Are you sure?')">
        </form>
    </div>

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