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

class ChangeVolumeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private User $user;
    private float $volume;
    private int $room_id;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, float $volume, int $room_id)
    {
        $this->volume = $volume;
        $this->user = $user;
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
            new PresenceChannel('presence.chat.'.$this->room_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'volume-change';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['username']),
            'volume' => $this->volume
        ];
    }
}
