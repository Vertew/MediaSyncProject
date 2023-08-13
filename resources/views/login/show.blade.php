@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="container">
    <div class = "row">
        <div class = "col-4">
        </div>

        <div class = "col-4 mt-3 text-center">
            <div class="card bg-light">
                <div class="card-header"><h1 class='display-6'>Login</h1></div>
                <form method="POST" action="{{ route('login.authenticate') }}">
                    @csrf
                    <div class="container text-start my-3">
                        <label for="email" class="form-label"><strong>Email</strong></label>
                        <input class="form-control" type = "text" name = "email" id='email' placeholder="Enter email..." value ="{{old('email')}}">
                    </div>
                    <div class="container text-start my-3">
                        <label for="password" class="form-label"><strong>Password</strong></label>
                        <input class="form-control" type = "password" name = "password" id='password' placeholder="Enter password..." value ="{{old('password')}}">
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-column mx-2">
                            <input class="btn btn-primary my-3 mx-1" type = "submit" value = "Login">
                        </div>
                    </div>
                </form>
            </div>

            <div class = "container-md mt-3">
                <a class="btn btn-link" href="{{route('users.create')}}">Don't have an account? Click here to register.</a>
            </div>

            <div class = "container-md mt-3">
                <form method="POST" action="{{ route('users.storeGuest') }}">
                    @csrf
                    <input class="btn btn-link" type = "submit" value = "Don't want an account? Continue as a guest.">
                </form>
            </div>
        </div>
        <div class = "col-4">
        </div>
    </div>
</div>



@endsection