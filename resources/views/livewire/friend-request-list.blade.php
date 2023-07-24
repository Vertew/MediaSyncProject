<div class = "container-fluid text-center">
    <div class = "row">
        <div class = "col-md-4">
            <h3>Friend requests</h3>
            <ul class = "list-group" id="request-list" style="max-height: 500px; overflow-y: auto;">
                @foreach(Auth::user()->notifications as $notification)
                    <li>
                        <span class="list-group-item">{{$notification->data['sender_name']}}
                            <button class="btn btn-success btn-sm" type="button" wire:click="acceptRequest({{$notification->data['sender_id']}}, '{{$notification->id}}')">Accept</button>
                            <button class="btn btn-danger btn-sm" type="button" wire:click="declineRequest('{{$notification->id}}')">Decline</button>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class = "col-md-4">
        </div>
        <div class = "col-md-4">
            <h3>Friends</h3>
            <ul class = "list-group" id="friend-list" style="max-height: 500px; overflow-y: auto;">
                @foreach(Auth::user()->friends as $friend)
                    <li>
                        <span class="list-group-item">{{$friend->username}}
                            <button class="btn btn-danger btn-sm" type="button" wire:click="unfriend({{$friend}})">Unfriend</button>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
