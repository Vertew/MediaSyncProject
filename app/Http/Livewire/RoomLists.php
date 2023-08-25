<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use Pusher\Pusher;

// This class handles the room list section on the home page of the web app.
class RoomLists extends Component
{
    private Pusher $pusher;
    public Collection $rooms;
    public Collection $my_rooms;
    public $user_array = [];
    public $channels = [];
    public $connection;

    public function mount(){

        // The basic idea behind this is storing two collections of rooms, those that the current user owns
        // along with those that are owned by their friends.

        $this->rooms = new Collection();
        $this->my_rooms = new Collection();

        // We access the list of active presence channels by creating an instance of the pusher class and then searching
        // through the active channel list for channels that have the presence channel name.

        $this->connection = config('broadcasting.connections.pusher');
        $this->pusher = new Pusher(
            $this->connection['key'],
            $this->connection['secret'],
            $this->connection['app_id'],
            $this->connection['options'] ?? []
        );

        $allChannels = ($this->pusher->get_channels()->channels);
        foreach ($allChannels as $channel => $object){
            if(str_contains($channel, 'presence-presence.chat.')){
                $this->channels[] =  $channel;
            }
        }

        // We populate the friend room collection and then search for each rooms' associated channel. If the channel is 
        // active, we can then get a list of the active users subscribed to the channel. This user list is then
        // added to the user_array at the key index representing the room.
        foreach(Auth::user()->friends as $friend){
            foreach($friend->rooms as $room){
                $this->rooms->push($room);
                if(in_array('presence-presence.chat.'.$room->id, $this->channels)) {
                    $userCollection = new Collection();
                    foreach($this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users')->users as $user){
                        $userCollection->push(User::find($user->id));
                    }
                    $this->user_array[$room->id] = $userCollection->toArray();
                }
            }
        }

        // We repeat the process with our own rooms.
        foreach(Auth::user()->rooms as $room){
            $this->my_rooms->push($room);
            if(in_array('presence-presence.chat.'.$room->id, $this->channels)) {
                $userCollection = new Collection();
                foreach($this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users')->users as $user){
                    $userCollection->push(User::find($user->id));
                }
                $this->user_array[$room->id] = $userCollection->toArray();
            }
        }

    }

    public function dump(){
        dd($this->user_array);
    }

    // Since we want this process to by dynamic, we then update all of the values as
    // needed in the update function which is called on a polling schedule. Since the pusher
    // instance is not able to be passed through javascript, we need to create a new one every
    // time we update unfortunately.
    public function update(){
        $this->pusher = new Pusher(
            $this->connection['key'],
            $this->connection['secret'],
            $this->connection['app_id'],
            $this->connection['options'] ?? []
        );

        $this->channels = [];


        $allChannels = ($this->pusher->get_channels()->channels);
        foreach ($allChannels as $channel => $object){
            if(str_contains($channel, 'presence-presence.chat.')){
                $this->channels[] =  $channel;
            }
        }

        
        foreach($this->my_rooms as $room){
            if(in_array('presence-presence.chat.'.$room->id, $this->channels)) {
                $userCollection = new Collection();
                foreach($this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users')->users as $user){
                    $userCollection->push(User::find($user->id));
                }
                $this->user_array[$room->id] = $userCollection->toArray();
            }else{
                $this->user_array[$room->id] = null;
            }
        }

        foreach(Auth::user()->friends as $friend){
            foreach($friend->rooms as $room){
                if(!$this->rooms->contains($room)){
                    $this->rooms->push($room);
                }
                if(in_array('presence-presence.chat.'.$room->id, $this->channels)) {
                    $userCollection = new Collection();
                    foreach($this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users')->users as $user){
                        $userCollection->push(User::find($user->id));
                    }
                    $this->user_array[$room->id] = $userCollection->toArray();
                }else{
                    $this->user_array[$room->id] = null;
                }
            }
        }
    }

    public function render()
    {
        RoomLists::update();
        return view('livewire.room-lists');
    }
}