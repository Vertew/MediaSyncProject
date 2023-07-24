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

        $recipient->notifications()->firstWhere('id', $notificationId)->delete();
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
