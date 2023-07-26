<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Pusher\Pusher;

class RoomLists extends Component
{
    private $pusher;
    public $rooms = [];
    public $my_rooms = [];
    public $channels = [];
    public $user_array = [];
    public $connection; 

    public function mount(){
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

        //dd($this->channels);

        foreach(Auth::user()->friends as $friend){
            foreach($friend->rooms as $room){
                $this->rooms[] = $room;
                if(in_array('presence-presence.chat.'.$room->id, $this->channels)) {
                    $this->user_array[$room->id] = $this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users');
                }
            }
        }

        foreach(Auth::user()->rooms as $room){
            $this->my_rooms[] = $room;
            if(in_array('presence-presence.chat.'.$room->id, $this->channels)) {
                $this->user_array[$room->id] = $this->pusher->get('/channels/presence-presence.chat.'.$room->id.'/users');
            }
        }

    }

    public function dump(){
        dd($this->user_array[3]['users']);
    }

    public function render()
    {
        return view('livewire.room-lists');
    }
}
