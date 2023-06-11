@extends('layouts.app')

@section('title', 'Video Room')

@section('content')

<div class = "container-md mt-3 text-center">

    <video width="1280" height="720" controls>
        <source src={{asset($video->path)}} type="video/mp4">
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