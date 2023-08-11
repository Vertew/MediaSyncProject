@extends('layouts.app')

@section('title', '- My profile')

@section('content')

    <div class="row text-center">
        <div class="col-4">
        </div>
        <div class="col-4">
            <div class="card bg-light">
                <div class="card-header"><h3>Profile information</h3></div>
                <div class="card-body"></div>
                    <ul class = 'list-group list-group-flush'>
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
                        <button class="btn btn-primary my-3" type="button">Edit profile</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-4">
        </div>
    </div>

@endsection