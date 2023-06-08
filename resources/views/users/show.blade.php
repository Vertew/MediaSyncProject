@extends('layouts.app')

@section('Hello '.$user->username)

@section('content')

    <div class="container-md mt-3 mb-5 border text-center">
        <h3>General</h3>
        <ul class = 'list-group'>
            <li class="list-group-item"><strong>User tag: </strong>{{$user->username}}</li>
            <li class="list-group-item"><strong>Email:</strong> {{$user->email}}</li>
            <li class="list-group-item">Joined {{$user->created_at}}</li>
        </ul>
    </div>

@endsection