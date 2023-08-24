@extends('layouts.app')

@section('title', ' - Edit Profile')

@section('content')

<div class = "container mb-5">
    <div class = "row">
        <div class = "col-4">
        </div>
        <div class = "col-4 mt-3 text-start">
            <div class="card bg-light">
                <div class="card-header"><h1 class='display-6 text-center'>Edit info</h1></div>
                <form method="POST" action="{{ route('profiles.update' , ['id'=> $profile->id])}}" enctype="multipart/form-data">
                    @csrf
                    <div class="container my-3">
                        <label for="name"><strong>Name</strong></label>
                        <input class="form-control" type = "text" name = "name" id='name' value = "{{$profile->name}}">
                    </div>
                    <div class="container my-3">
                        <label for="status"><strong>Status</strong></label>
                        <input class="form-control" type = "text" name = "status" id='status' value = "{{$profile->status}}">
                    </div>
                    <div class="container my-3">
                        <label for="location"><strong>Location</strong></label>
                        <input class="form-control" type = "text" name = "location" id='location' value = "{{$profile->location}}">
                    </div>
                    <div class="container my-3">
                        <label for="date"><strong>Date of Birth</strong></label>
                        <input class="form-control" type = "date" name = "date_of_birth" id='date' value = "{{$profile->date_of_birth}}">
                    </div>
                    <div class="container my-3">
                        <label for="picture"><strong>Add/change profile picture</strong></label>
                        <input class="form-control" type = "file" name = "picture" id='picture'>
                    </div>
                    <div class="container-md mb-3 mt-3">
                        <label for="checkbox"><strong>Remove profile picture</strong></label>
                        <input type="checkbox" id="checkbox" name = "checkbox">
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-column mx-2">
                            <input class="btn btn-primary m-1" type = "submit" value = "Update">
                            <a class="btn btn-danger m-1" href="{{route('profiles.show',['id'=> $profile->id])}}">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class = "col-4">
        </div>
    </div>
</div>
@endsection