<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomDeletedEvent implements ShouldBroadcast
{

    private int $room_id;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(int $room_id)
    {
        $this->room_id = $room_id;
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
        return 'room-deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->room_id,
        ];
    }

}
