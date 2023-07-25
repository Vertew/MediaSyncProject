<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;

class FriendRequestList extends Component
{

    public function acceptRequest(int $sender_id, string $notificationId) {
        $recipient = Auth::user();
        $sender = User::find($sender_id);

        $recipient->friends()->attach($sender);
        $sender->friends()->attach($recipient);

        // Deletes the notification/request and if the sender also had a request from this current user,
        // that notification is deleted as well.
        $recipient->notifications()->firstWhere('id', $notificationId)->delete();
        $sender->notifications()->firstWhere('data->sender_id', $recipient->id)?->delete();
    }

    public function declineRequest(string $notificationId) {
        Auth::user()->notifications()->firstWhere('id', $notificationId)->delete();
    }

    public function unfriend(User $friend) {
        $user = Auth::user();

        $user->friends()->wherePivot('user2_id', $friend->id)->detach();
        $friend->friends()->wherePivot('user2_id', $user->id)->detach();
    }

    public function render()
    {
        return view('livewire.friend-request-list');
    }
}
