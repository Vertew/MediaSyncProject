@extends('layouts.app')

@section('title', 'Showing Video')

@section('content')

<div class = "container-md mt-3 text-center">

    <video width="1280" height="720" controls>
        <source src="http://127.0.0.1:8080/media/videos/LuxeaRec2023-01-01_12-58-08.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>

    <a href="{{route('videos.create')}}">
        <button class="btn btn-light" type="button">Upload video</button>
    </a>

    <a href="{{route('videos.index_user')}}">
        <button class="btn btn-light" type="button">Select video</button>
    </a>

</div>

@endsection