@extends('layouts.app')

@section('title', '- My profile')

@section('content')

<div class = "container mb-5">
    <div class="row text-center">
        <div class="col-4">
        </div>
        <div class="col-4">
            <div class="card bg-light">
                <div class="card-header"><h1 class="display-6">Profile information</h1></div>
                    <ul class = 'list-group list-group-flush text-start'>
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
                    <div class="card-footer">
                        <a href="{{route('profiles.edit', ['id'=> $profile->id])}}">
                            <div class="d-flex flex-column mx-2">
                                <button class="btn btn-primary m-1" type="button">Edit profile</button>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
        </div>
    </div>
</div>

@endsection