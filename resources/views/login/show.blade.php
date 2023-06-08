@extends('layouts.app')

@section('title', 'Login')

@section('content')


<div class = "container-md mt-3 text-center">

    <h3 class='display-6 text-center'>Enter login details</h3>
        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf
            <div class="container-md mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control" type = "text" name = "email" id='email' placeholder="Enter email..." value ="{{old('email')}}">
            </div>
            <div class="container-md mb-3 mt-3">
                <label for="password" class="form-label">Password:</label>
                <input class="form-control" type = "password" name = "password" id='password' placeholder="Enter password..." value ="{{old('password')}}">
            </div>
            <input class="btn btn-primary" type = "submit" value = "Login">
        </form>
    <div class = "container-md mt-3">

    </div>

    <div class = "container-md mt-3">
        <a class="btn btn-link" href="{{route('users.create')}}">Don't have an account? Click here to register.</a>
    </div>

</div>

@endsection