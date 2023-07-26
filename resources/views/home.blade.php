@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container-md mt-3 text-center">

    <p class="h6">On the home page you can see all of your rooms as well as your friend's rooms.</p>

    <livewire:room-lists />   

    {{-- Legacy room
    <div class="container-md mt-3 text-center">
        <a href="{{route('videos.show', ['id'=> 1])}}">
            <button class="btn btn-success" type="button">Legacy room</button>
        </a>
    </div>
    --}}
</div>

@endsection