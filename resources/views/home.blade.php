@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container-md mt-3 text-center">

    <h3 class='display-6 text-center'>Your rooms</h3>

    @foreach (Auth::user()->rooms as $room)
        <div class="container-md mt-3">
            <div class="list-group">
                <a class="list-group-item list-group-item-action" href = "{{route('rooms.show', ['key'=> $room->key])}}"> Room {{$room->id}}</a>
            </div>
        </div>
    @endforeach

    <div class="container-md mt-3 text-center">
        <form action="{{route('rooms.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <button type="submit" class="btn btn-success"> Create a room</button>
        </form>
    </div>

</div>

@endsection