<div class = "container-fluid text-center">
    <div class = "row">
        <div class = "col-md-4">
            @if(Auth::id() == $user->id)
                <h3>Friend requests</h3>
                <ul class = "list-group" id="request-list" style="max-height: 500px; overflow-y: auto;" wire:poll>
                    @forelse(Auth::user()->notifications as $notification)
                        <li class="list-group-item text-bg-light">
                            <span><strong>{{$notification->data['sender_name']}}</strong>
                                <a href="{{route('users.show', ['id'=> $notification->data['sender_id']])}}" class="btn btn-sm btn-primary">View Profile</a>
                                <button class="btn btn-success btn-sm" type="button" wire:click="acceptRequest({{$notification->data['sender_id']}}, '{{$notification->id}}')">Accept</button>
                                <button class="btn btn-danger btn-sm" type="button" wire:click="declineRequest('{{$notification->id}}')">Decline</button>
                            </span>
                        </li>
                    @empty
                        <p>No friend requests at the moment.</p>
                    @endforelse
                </ul>
            @endif
        </div>
        <div class = "col-md-4">
        </div>
        <div class = "col-md-4">
            <h3>Friends</h3>
            <ul class = "list-group p-2" id="friend-list" style="max-height: 500px; overflow-y: auto;">
                @forelse($user->friends as $friend)
                    <li class="list-group-item text-bg-light">
                        <span><strong>{{$friend->profile->name ?? "Annonymous"}} <small>({{$friend->username}})</small></strong>
                            <a href="{{route('users.show', ['id'=> $friend->id])}}" class="btn btn-sm btn-primary">View Profile</a>
                            @if(Auth::id() == $user->id)
                                <button class="btn btn-danger btn-sm" type="button" wire:click="unfriend({{$friend}})">Unfriend</button>
                            @endif
                        </span>
                    </li>
                @empty
                    <p>Add some friends to see this list fill out.</p>
                @endforelse
            </ul>
        </div>
    </div>
</div>
