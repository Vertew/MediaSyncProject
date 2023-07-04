@extends('layouts.app')

@section('title', '- My account')

@section('content')

    <h3 class = "text-center display-5">Hi {{$user->profile->name ?? $user->username}}!</h3>

    <div class="container-md mt-3 text-center">
        <h3>General</h3>
        <ul class = 'list-group'>
            <li class="list-group-item"><strong>User tag: </strong>{{$user->username}}</li>
            <li class="list-group-item"><strong>Email: </strong> {{$user->email}}</li>
            <li class="list-group-item">Joined {{$user->created_at}}</li>
        </ul>
    </div>

    <div class="container-md mt-3 mb-5 text-center">
        <a  href="{{route('profiles.show', ['id'=> $user->profile->id])}}">
            <button class="btn btn-primary" type="button">View Profile</button>
        </a>
    </div>

@endsection