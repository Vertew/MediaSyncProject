@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="container-md mt-3 text-center">
    <form action="{{route('rooms.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="container-md mb-3 mt-3">
            <label for="name" class="form-label">Room name:</label>
            <input class="form-control" type = "text" name = "name" id="name" value ="{{old('name')}}">
        </div>
        <div class="container-md mb-3 mt-3">
            <button type="submit" class="btn btn-success"> Create a room</button>
        </div>
    </form>
</div>

@endsection