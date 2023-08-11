@extends('layouts.app')

@section('title', '- My account')

@section('content')

    <h3 class = "text-center display-5">{{$user->profile->name ?? $user->username}}</h3>


    <div class="row mt-2">
        <div class = "col-4">
        </div>
        <div class = "col-4 text-center">
            <div class="container">
                <div class="card bg-light">
                    <div class="card-header"><h3>{{$user->username}}</h3></div>
                    <div class="card-body">
                        <ul class = 'list-group'>
                            <li class = "list-group-item">{{$user->email}}</li>
                            <li class = "list-group-item">Joined {{$user->created_at}}</li>
                            <li class = "list-group-item">
                                <a class="btn btn-primary"  href="{{route('profiles.show', ['id'=> $user->profile->id])}}">View Details</a>
                                @if($user->id != Auth::id() && Auth::user()->friends->doesntContain($user->id))
                                    <form method="POST" action="{{route('users.sendRequest', ['id'=> $user->id])}}">
                                        @csrf
                                        <input class="btn btn-success mt-1" type = "submit" value = "Add Friend">
                                    </form>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-4">
        </div>

    </div>


    <livewire:friend-request-list :user="$user">

@endsection