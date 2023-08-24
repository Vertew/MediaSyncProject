@extends('layouts.app')

@section('title', '- My account')

@section('content')

<div class = "container mb-5">
    <div class="row">
        <div class = "col-4">
        </div>
        <div class = "col-4 text-center">
            <div class="card bg-light">
                <div class="card-header d-flex align-items-center justify-content-center overflow-hidden"><img class="img-fluid rounded-circle me-2" style="height: 75px; width: 75px;" src="{{url($user->picture)}}" alt="Profile Picture"><h1 class="display-6">{{$user->username}}</h1></div>
                    <ul class = 'list-group list-group-flush text-start'>
                        <li class = "list-group-item"><strong>Email: </strong>{{$user->email}}</li>
                        <li class = "list-group-item"><strong>Joined: </strong> {{$user->created_at}}</li>
                    </ul>
                    <div class="card-footer">
                        <div class="d-flex flex-column mx-2">
                            <a class="btn btn-primary m-1"  href="{{route('profiles.show', ['id'=> $user->profile->id])}}">View Details</a>
                            @if($user->id != Auth::id() && Auth::user()->friends->doesntContain($user->id))
                                <form method="POST" action="{{route('users.sendRequest', ['id'=> $user->id])}}">
                                    @csrf
                                    <div class="d-flex flex-column">
                                        <input class="btn btn-success m-1" type = "submit" value = "Add Friend">
                                    </div>
                                </form>
                            @elseif($user->id == Auth::id())
                                <form method="POST" action="{{ route('users.destroy', ['id'=> $user->id])}}">
                                    @csrf
                                    <div class="d-flex flex-column">
                                        @method('DELETE')
                                        <input class="btn btn-danger m-1" type = "submit" value = "Delete Account" onclick="return confirm('Are you sure? This action is irreversible.')">
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-4">
        </div>
    </div>
    <livewire:friend-request-list :user="$user">
</div>

@endsection