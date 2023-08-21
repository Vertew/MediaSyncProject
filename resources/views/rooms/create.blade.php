@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class = "row">
    <div class = "col-md-4">
    </div>
    <div class = "col-md-4 mt-3 text-center">
        <div class="card bg-light">
            <div class="card-header"><h1 class='display-6 text-center'>Name your room</h1></div>
            <form action="{{route('rooms.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="container-md mb-3 mt-3">
                    <input class="form-control" type = "text" name = "name" id="name" value ="{{old('name')}}">
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Create room</button>
                </div>
            </form>
        </div>
    </div>
    <div class = "col-md-4">
    </div>
</div>

@endsection