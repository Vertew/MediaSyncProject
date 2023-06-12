@extends('layouts.app')

@section('title', 'Video Room')

@section('content')

<div class = "container-md mt-3 text-center">

    <livewire:video-index />

    <a href="{{route('videos.create')}}">
        <button class="btn btn-light" type="button">Upload video</button>
    </a>

</div>

@endsection