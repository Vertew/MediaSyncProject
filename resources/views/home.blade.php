@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container-md mt-3 text-center">

    <h3 class='display-6'>Your rooms</h3>

    @forelse (Auth::user()->rooms as $room)
        <div class="container-sm mt-3">
            <div class="list-group">
                <a class="list-group-item list-group-item-action" href = "{{route('rooms.show', ['key'=> $room->key])}}">{{$room->name}}</a>
            </div>
        </div>
    @empty
        <p>No rooms just yet...</p>
    @endforelse

    <div class="container-md mt-3 text-center">
        <a href="{{route('rooms.create')}}">
            <button class="btn btn-success" type="button">Create room</button>
        </a>
    </div>

    <div class="container-md mt-5">
        <h3 class='display-6 text-center'>Friends' rooms</h3>
    </div>

    <livewire:friend-rooms />

    {{-- Legacy room
    <div class="container-md mt-3 text-center">
        <a href="{{route('videos.show', ['id'=> 1])}}">
            <button class="btn btn-success" type="button">Legacy room</button>
        </a>
    </div>
    --}}
</div>

@endsection