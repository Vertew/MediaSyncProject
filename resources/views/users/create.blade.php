@extends('layouts.app')

@section('title', ' - Register')

@section('content')

<div class = "container mb-5">
    <div class = "row">
        <div class = "col-4">
        </div>

        <div class = "col-4 mt-3 text-center">
            <div class="card bg-light">
                <div class="card-header"><h1 class='display-6'>Register</h1></div>
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <p class="mt-2"><strong>All fields are required</strong></p>
                    <div class="text-start">   
                        <div class="container my-3">
                            <label for="username" class="form-label"><strong>Username</strong></label>
                            <input class="form-control" type = "text" name = "username" id="username"  placeholder="Enter username..." value ="{{old('username')}}">
                        </div>
                        <div class="container my-3">
                            <label for="email" class="form-label"><strong>Email</strong></label>
                            <input class="form-control" type = "text" name = "email" id="email" placeholder="Enter email..." value ="{{old('email')}}">
                        </div>
                        <div class="container my-3">
                            <label for="password" class="form-label"><strong>Password</strong></label>
                            <input class="form-control" type = "password" name = "password" id="password" placeholder="Enter password..." value ="{{old('password')}}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-column mx-2">
                            <input class="btn btn-primary m-1" type = "submit" value = "Register">
                            <a class="btn btn-danger m-1" href="{{route('home')}}">Cancel</a>
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