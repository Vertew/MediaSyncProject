@extends('layouts.app')

@section('title', ' - Editing...')

@section('content')

    <div class = "container-md mt-3">
        <form method="POST" action="{{ route('profiles.update' , ['id'=> $profile->id])}}" enctype="multipart/form-data">
            @csrf
            <div class="container-md mb-3 mt-3">
                <label for="name">Name:</label>
                <input class="form-control" type = "text" name = "name" id='name' value = "{{$profile->name}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <label for="date">Date of Birth:</label>
                <input class="form-control" type = "date" name = "date_of_birth" id='date' value = "{{$profile->date_of_birth}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <label for="status">Status:</label>
                <input class="form-control" type = "text" name = "status" id='status' value = "{{$profile->status}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <label for="location">Location:</label>
                <input class="form-control" type = "text" name = "location" id='location' value = "{{$profile->location}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <input class="btn btn-primary" type = "submit" value = "Update">
            </div>
            <div class="container-md mb-3 mt-3">
                <a class="btn btn-secondary" href="{{route('profiles.show',['id'=> $profile->id])}}">Cancel</a>
            </div>
        </form>
    </div>
@endsection