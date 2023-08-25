<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Events\RequestAcceptedEvent;
use App\Events\UserUnfriendedEvent;
use Livewire\Component;
use App\Models\User;

// This function handles the friend request list logic on the user account page.
// Fairly straightforward.

class FriendRequestList extends Component
{
    public $user;

    protected $listeners = ['friendsUpdated' => '$refresh'];

    public function acceptRequest(int $sender_id, string $notificationId) {
        if(Gate::allows('private', $this->user->id)){
            $recipient = Auth::user();
            $sender = User::find($sender_id);

            $recipient->friends()->attach($sender);
            $sender->friends()->attach($recipient);

            // Deletes the notification/request and if the sender also had a request from this current user,
            // that notification is deleted as well.
            $recipient->notifications()->firstWhere('id', $notificationId)->delete();
            $sender->notifications()->firstWhere('data->sender_id', $recipient->id)?->delete();

            RequestAcceptedEvent::dispatch($sender->id, $recipient);
            $this->emitTo('top-bar', 'friendsUpdated');
            $this->emitSelf('friendsUpdated');
        }
    }

    public function declineRequest(string $notificationId) {
        if(Gate::allows('private', $this->user->id)){
            Auth::user()->notifications()->firstWhere('id', $notificationId)->delete();
            $this->emitTo('top-bar', 'friendsUpdated');
        }
    }

    public function unfriend(User $friend) {
        if(Gate::allows('private', $this->user->id)){
            $user = Auth::user();

            $user->friends()->wherePivot('user2_id', $friend->id)->detach();
            $friend->friends()->wherePivot('user2_id', $user->id)->detach();

            UserUnfriendedEvent::dispatch($friend->id, $user);
            $this->emitSelf('friendsUpdated');
        }
    }
    

    public function render()
    {
        return view('livewire.friend-request-list');
    }
}
