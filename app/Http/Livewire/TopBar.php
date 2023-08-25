<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Room;
use Pusher\Pusher;

// This livewire component contains any dynamic functionality needed to be persistent across any page.

class TopBar extends Component
{

    public $notifCount;
    public $user;

    public function mount() {
        $this->user = Auth::user();
        $this->notifCount = Auth::user()->unreadNotifications->count();
    }

    public function getListeners()
    {
        return [
            "echo-presence:presence.chat.0,.room-deleted" => 'exitRoom',
            "echo-private:private.user.{$this->user->id},.request-recieved" => 'updateBadge',
            "friendsUpdated" => "updateBadge",
        ];
    }

    // This function allows users to exit gracefully from the room they're in if it's deleted when
    // they're in it.
    
    public function exitRoom(array $event) {
        $room_id = $event['room_id'];

        $connection = config('broadcasting.connections.pusher');
        $pusher = new Pusher(
            $connection['key'],
            $connection['secret'],
            $connection['app_id'],
            $connection['options'] ?? []
        );

        $allChannels = ($pusher->get_channels()->channels);
        foreach ($allChannels as $channel => $object){
            if(str_contains($channel, 'presence-presence.chat.'.$room_id)){
                $users = $pusher->get('/channels/presence-presence.chat.'.$room_id.'/users')->users;
                foreach ($users as $user){
                    if($user->id == Auth::user()->id){
                        return redirect()->route('home');
                    }
                }
            }
        }
    }

    public function updateBadge() {
        $this->notifCount = Auth::user()->unreadNotifications->count();
    }


    public function render()
    {
        return view('livewire.top-bar');
    }
}
