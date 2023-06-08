@extends('layouts.app')

@section('title', ' - Register')

@section('content')

    <div class = "container-md mt-3">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <p>All fields are required</p>
            <div class="container-md mb-3 mt-3">
                <label for="username" class="form-label">Username:</label>
                <input class="form-control" type = "text" name = "username" id="username" value ="{{old('username')}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control" type = "text" name = "email" id="email" value ="{{old('email')}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <label for="password" class="form-label">Password:</label>
                <input class="form-control" type = "password" name = "password" id="password" value ="{{old('password')}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <input class="btn btn-primary" type = "submit" value = "Submit">
            </div>
            <div class="container-md mb-3 mt-3">
                <a class="btn btn-secondary" href="{{route('home')}}">Cancel</a>
            </div>
        </form>
    </div>

@endsection