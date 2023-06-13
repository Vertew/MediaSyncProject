@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container-md mt-3 text-center">

    <h3 class='display-6 text-center'>Your rooms</h3>

    @foreach (Auth::user()->rooms as $room)
        <div class="container-md mt-3">
            <div class="list-group">
                <a class="list-group-item list-group-item-action" href = "{{route('rooms.show', ['key'=> $room->key])}}">{{$room->name}}</a>
            </div>
        </div>
    @endforeach

    <div class="container-md mt-3 text-center">
        <a href="{{route('rooms.create')}}">
            <button class="btn btn-success" type="button">Create room</button>
        </a>
    </div>

</div>

@endsection