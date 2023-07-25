
{{-- This whole thing is extremely anti-laravel convention but honestly sometimes laravel convention
     just doesn't lend itself to some of the more dynamic elements of the project like this. --}}
@forelse ($rooms as $room)
    <div class="container-sm mt-3">
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href = "{{route('rooms.show', ['key'=> $room->key])}}">{{$room->name}} - Owner: {{$room->user->username}}</a>
            @if(isset($user_array[$room->id]))
                @foreach ($user_array[$room->id]->users as $user)
                    {{-- This if statement is a bit of a cheaty way to get around the fact that the websocket channel doesn't update
                        all clients quick enough to avoid displaying your name in the list of online users if you just left the room.
                        You could refresh the list periodically but atm it doesn't seem worth the effort --}}
                    <p hidden>{{$username = App\Models\User::find($user->id)->username}}</p>
                    @if($username != Auth::user()->username)
                        <p>{{$username}}</p>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@empty
    <p>Add some friends to see this fill out.</p>
@endforelse
