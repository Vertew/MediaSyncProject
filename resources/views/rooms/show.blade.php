@extends('layouts.app')

@section('title', 'Room')

@section('content')

<div class = "container-md mt-3 text-center">

    <h1 class='display-5 text-center'>{{$room->name}}</h1>

    <livewire:video-room />

    <div class = "container-md mt-5 text-center">
        <button class="btn btn-primary" onclick="showhide()"> Upload new video</button>
    </div>

    <div class = "container-md mt-3 text-center" id = "upload-div" style="display: none">
        <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="container-md mt-3">
                    <input type="file" name="video" class="form-control"/>
                </div>
                <div class="container-md mt-3">
                    <button type="submit" class="btn btn-success">Upload</button>
                    <button type="reset" onclick="showhide()" class="btn btn-secondary">Cancel</button>
                </div>
        </form>
    </div>

</div>

<div class="mt-5 p-4 bg-dark text-white text-center"></div>

<script>
    function showhide() {

        var div = document.getElementById("upload-div");
        if (div.style.display === "none") {
            div.style.display = "block";
        } else {
            div.style.display = "none";
         }
    }
</script>

@endsection