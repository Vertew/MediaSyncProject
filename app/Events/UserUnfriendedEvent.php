<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserUnfriendedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(int $id, User $user)
    {
        $this->id = $id;
        $this->user = $user;
    }


   /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private.user.'.$this->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'friend-removed';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->username,
        ];
    }
}
