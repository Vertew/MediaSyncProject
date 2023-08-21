
{{-- This whole thing is extremely anti-laravel convention but honestly sometimes laravel convention
     just doesn't lend itself to some of the more dynamic elements of the project like this. It's also
     very much a work around for the fact the weird issue with Livewire converting model instances into 
     arrays after the initial render which breaks everything. --}}
<div class = "row">
    <div class = "col-md-6">

        <div class="card bg-light mt-5" style="max-height: 500px; overflow-y: auto;" wire:poll>
            <div class="card-header"><h3 class='display-6'>Your rooms</h3></div>
            <div class="container pb-3">
                @forelse ($my_rooms as $room)
                    <div class="list-group mt-3">
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href = "{{route('rooms.show', ['key'=> $room->key])}}">
                            <b>{{$room->name}}</b>
                            <div class="d-flex justify-content-end">
                                @if($room->locked)
                                    <span class="badge bg-danger rounded-pill mx-1">Locked</span>
                                @else
                                    <span class="badge bg-success rounded-pill mx-1">Open</span>
                                @endif
                                @if(isset($user_array[$room->id]))
                                    <span class="badge bg-success rounded-pill mx-1">{{count($user_array[$room->id])}}</span>
                                @else
                                    <span class="badge bg-primary rounded-pill mx-1">0</span>
                                @endif
                            </div>
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-room-{{$room->id}}">Online Users</button>
                    </div>
                    <div class="modal" id="modal-room-{{$room->id}}" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Active users in {{$room->name}}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if(isset($user_array[$room->id]))
                                        <ul class="list-group">
                                            @foreach ($user_array[$room->id] as $user)
                                                <li class="list-group-item text-bg-light">
                                                    <strong>{{$user['username']}}</strong>
                                                    <a href="{{route('users.show', ['id'=> $user['id']])}}" class="btn btn-sm btn-primary mx-1">View Profile</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>No active users in here at the moment.</p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="mt-3">Create a room to get started!</p>
                @endforelse
            </div>
            <div class="card-footer">
                <a href="{{route('rooms.create')}}">
                    <button class="btn btn-success" type="button">Create room</button>
                </a>
            </div>
        </div> 
    </div>
    <div class = "col-md-6">
        <div class="card bg-light mt-5" style="max-height: 500px; overflow-y: auto;">
            <div class="card-header"><h3 class='display-6 text-center'>Friends' rooms</h3></div>
            <div class="container pb-3">
                @forelse ($rooms as $room)
                    <div class="list-group mt-3">
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href = "{{route('rooms.show', ['key'=> $room->key])}}">
                            <b>{{$room->name}} - {{$room->user->username}}</b>
                            <div class="d-flex justify-content-end">
                                @if($room->locked)
                                    <span class="badge bg-danger rounded-pill mx-1">Locked</span>
                                @else
                                    <span class="badge bg-success rounded-pill mx-1">Open</span>
                                @endif
                                @if(isset($user_array[$room->id]))
                                    <span class="badge bg-success rounded-pill mx-1">{{count($user_array[$room->id])}}</span>
                                @else
                                    <span class="badge bg-primary rounded-pill mx-1">0</span>
                                @endif
                            </div>
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-room-{{$room->id}}">Online Users</button>
                    </div>
                    <div class="modal" id="modal-room-{{$room->id}}" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Active users in {{$room->name}}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if(isset($user_array[$room->id]))
                                        <ul class="list-group">
                                            @foreach ($user_array[$room->id] as $user)
                                                <li class="list-group-item text-bg-light">
                                                    <strong>{{$user['username']}}</strong>
                                                    @if(!$user['guest'])
                                                        <a href="{{route('users.show', ['id'=> $user['id']])}}" class="btn btn-sm btn-primary mx-1">View Profile</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>No active users in here at the moment.</p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    @if(Auth::user()->guest)
                        <p class="mt-3">Create an account to add friends.</p>   
                    @else
                        <p class="mt-3">Add some friends to see this fill out.</p>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
    {{-- <button class="btn btn-light mt-3" id="dump" type="button" wire:click="dump"><b>Dump</b></button> --}}
</div>
