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
use App\Models\Room;

class UserUnbannedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private User $recipient;
    private User $user;
    private Room $room;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, User $recipient, Room $room)
    {
        $this->user = $user;
        $this->recipient = $recipient;
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence.chat.0'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user-unbanned';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['username', 'id']),
            'recipient' => $this->recipient->only(['username','id']),
            'room' => $this->room->only(['name','id']),
        ];
    }
}
