@extends('layouts.app')

@section('title', '- '.$profile->user->username)

@section('content')

    <div class="container-md mt-3 text-center">
        <h3>Profile information</h3>
        <ul class = 'list-group'>
            @if ($profile->name != null)
                <li class="list-group-item"><strong>Name: </strong>{{$profile->name ?? 'Anonymous'}}</li>
            @endif
            @if ($profile->status != null)
                <li class="list-group-item"><strong>Status: </strong>{{$profile->status}}</li>
            @endif
            @if ($profile->location != null)
                <li class="list-group-item"><strong>Location: </strong>{{$profile->location}} </li>
            @endif
            @if ($profile->date_of_birth != null)
                <li class="list-group-item"><strong>Date of Birth: </strong>{{$profile->date_of_birth}}</li>
            @endif  
            <li class="list-group-item"><strong>Email: </strong>{{$profile->user->email}}</li>
        </ul>
        <a href="{{route('profiles.edit', ['id'=> $profile->id])}}">
            <button class="btn btn-primary mt-2" type="button">Edit profile</button>
        </a>
    </div>

@endsection