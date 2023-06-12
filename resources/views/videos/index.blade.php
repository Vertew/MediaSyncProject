@extends('layouts.app')

@section('title')

@section('content')

  
    @foreach ($videos as $video)
        <div class="container-md mt-3">  
            <div class="list-group">
                <a class="list-group-item list-group-item-action" href = "{{route('videos.show', ['id' => $video->id])}}"> {{$video->title}}</a>
            </div>
        </div>
    @endforeach

@endsection