<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use Pusher\Pusher;


class RoomLists extends Component
{
    private Pusher $pusher;
    public Collection $rooms;
    public Collection $my_rooms;
    public $user_array = [];
    public $connection;

    public function mount(){

        $this->rooms = new Collection();
        $this->my_rooms = new Collection();

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
                $channels[] =  $channel;
            }
        }

        //dd($this->channels);

        foreach(Auth::user()->friends as $friend){
            foreach($friend->rooms as $room){
                $this->rooms->push($room);
                if(in_array('presence-presence.chat.'.$room->id, $channels)) {
                    $userCollection = new Collection();
                    foreach($this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users')->users as $user){
                        $userCollection->push(User::find($user->id));
                    }
                    $this->user_array[$room->id] = $userCollection->toArray();
                }
            }
        }

        foreach(Auth::user()->rooms as $room){
            $this->my_rooms->push($room);
            if(in_array('presence-presence.chat.'.$room->id, $channels)) {
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

    public function update(){
        $this->pusher = new Pusher(
            $this->connection['key'],
            $this->connection['secret'],
            $this->connection['app_id'],
            $this->connection['options'] ?? []
        );


        $allChannels = ($this->pusher->get_channels()->channels);
        foreach ($allChannels as $channel => $object){
            if(str_contains($channel, 'presence-presence.chat.')){
                $channels[] =  $channel;
            }
        }

        
        foreach($this->my_rooms as $room){
            if(in_array('presence-presence.chat.'.$room->id, $channels)) {
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
                if(in_array('presence-presence.chat.'.$room->id, $channels)) {
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
