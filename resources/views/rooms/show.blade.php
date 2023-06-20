@extends('layouts.app')

@section('title')

@section('content')

<div class = "container-md mt-3 text-center">

    <h1 class='display-5 text-center'>{{$room->name}}</h1>

    <livewire:video-room />

    <div class = "container-md mt-5 text-center">
        <button class="btn btn-primary" onclick="showhide('upload-div')"> Upload media</button>
    </div>

    <div class = "container-md mt-3 text-center" id = "upload-div" style="display: none">
        <livewire:file-upload />
    </div>
    
    {{--
    <div class = "container-md mt-3 text-center" id = "upload-div" style="display: none">
        <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="container-md mt-3">
                    <input type="file" name="file" class="form-control"/>
                </div>
                <div class="container-md mt-3">
                    <button type="submit" class="btn btn-success">Upload</button>
                    <button type="reset" onclick="showhide('upload-div')" class="btn btn-secondary">Cancel</button>
                </div>
        </form>
    </div>
    --}}

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