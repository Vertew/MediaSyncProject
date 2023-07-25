<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Pusher\Pusher;

class FriendRooms extends Component
{

    private $pusher;
    public $rooms = [];
    public $channels = [];
    public $user_array = [];

    public function mount(){
        $connection = config('broadcasting.connections.pusher');
        $this->pusher = new Pusher(
            $connection['key'],
            $connection['secret'],
            $connection['app_id'],
            $connection['options'] ?? []
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


        // $allChannels = ($this->pusher->get_channels()->channels);
        // foreach ($allChannels as $channel => $object){
        //     if(str_contains($channel, 'presence-presence.chat.')){
        //         $this->channels[] =  $channel;
        //     }
        // }

  
        //dd($this->user_array[2]->users);

        //dd($this->rooms[0]->id);
        //dd($this->pusher->get('/channels/presence-presence.chat.2/users'));
    }

    public function dump(){
        dd($this->pusher->get_channels());
    }

    public function render()
    {
        return view('livewire.friend-rooms');
    }
}
