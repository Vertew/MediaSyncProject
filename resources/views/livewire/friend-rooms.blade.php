
{{-- This whole thing is extremely anti-laravel convention but honestly sometimes laravel convention
     just doesn't lend itself to some of the more dynamic elements of the project like this. It's also
     very much a work around for the fact the weird issue with Livewire converting model instances into 
     arrays after the initial render which breaks everything. --}}
<div class = "row">
    <div class = "col-md-6">
        <div class="container-md mt-5">
            <h3 class='display-6'>Your rooms</h3>
        </div>

        @forelse ($my_rooms as $room)
            <div class="container-sm mt-3">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href = "{{route('rooms.show', ['key'=> $room->key])}}"--}}>
                        <b>{{$room->name}}</b>
                        @if(isset($user_array[$room->id]))
                            @foreach ($user_array[$room->id]->users as $user)
                                @if($user->id == Auth::user()->id)
                                    <p hidden>{{$inList=true}}</p>
                                    @break
                                @else
                                    <p hidden>{{$inList=false}}</p>
                                @endif
                            @endforeach
                            @if($inList)
                                <span class="badge bg-success rounded-pill">{{count($user_array[$room->id]->users) - 1}}</span>
                            @else
                                <span class="badge bg-success rounded-pill">{{count($user_array[$room->id]->users)}}</span>
                            @endif
                        @else
                            <span class="badge bg-primary rounded-pill">0</span>
                        @endif
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
                                        @foreach ($user_array[$room->id]->users as $user)
                                            <p hidden>{{$username = App\Models\User::find($user->id)->username}}</p>
                                            @if($username != Auth::user()->username)
                                                <li class="list-group-item">{{$username}}</li>
                                            @endif
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
            </div>
        @empty
            <p>Create a room to get started!</p>
        @endforelse

        {{-- @forelse (Auth::user()->rooms as $room)
            <div class="container-sm mt-3">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action" href = "{{route('rooms.show', ['key'=> $room->key])}}">{{$room->name}}</a>
                </div>
            </div>
        @empty
            <p>No rooms just yet...</p>
        @endforelse --}}

        <div class="container-md mt-3 text-center">
            <a href="{{route('rooms.create')}}">
                <button class="btn btn-success" type="button">Create room</button>
            </a>
        </div>
    </div>
    <div class = "col-md-6">
        <div class="container-md mt-5">
            <h3 class='display-6 text-center'>Friends' rooms</h3>
        </div>
        @forelse ($rooms as $room)
            <div class="container-sm mt-3">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href = "{{route('rooms.show', ['key'=> $room->key])}}"--}}>
                        <b>{{$room->name}} - {{$room->user->username}}</b>
                        @if(isset($user_array[$room->id]))
                            @foreach ($user_array[$room->id]->users as $user)
                                @if($user->id == Auth::user()->id)
                                    <p hidden>{{$inList=true}}</p>
                                    @break
                                @else
                                    <p hidden>{{$inList=false}}</p>
                                @endif
                            @endforeach
                            @if($inList)
                                <span class="badge bg-success rounded-pill">{{count($user_array[$room->id]->users) - 1}}</span>
                            @else
                                <span class="badge bg-success rounded-pill">{{count($user_array[$room->id]->users)}}</span>
                            @endif
                        @else
                            <span class="badge bg-primary rounded-pill">0</span>
                        @endif
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
                                        @foreach ($user_array[$room->id]->users as $user)
                                            {{-- This if statement is a bit of a cheaty way to get around the fact that the websocket channel doesn't update
                                                all clients quick enough to avoid displaying your name in the list of online users if you just left the room. --}}
                                            <p hidden>{{$username = App\Models\User::find($user->id)->username}}</p>
                                            @if($username != Auth::user()->username)
                                                <li class="list-group-item">{{$username}}</li>
                                            @endif
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
            </div>
        @empty
            <p>Add some friends to see this fill out.</p>
        @endforelse
    </div>
    {{-- <button class="btn btn-light mt-3" id="dump" type="button" wire:click="dump"><b>Dump</b></button> --}}
</div>
