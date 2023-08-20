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

class ChangeTimeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private User $user;
    private float $time;
    private string $symbol;
    private int $room_id;
    
    /**
     * Create a new event instance.
     */
    public function __construct(User $user, float $time, string $symbol ,int $room_id)
    {
        $this->user = $user;
        $this->time = $time;
        $this->symbol = $symbol;
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
        return 'time-change';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['username']),
            'time' => $this->time,
            'symbol' => $this->symbol,
        ];
    }
}
