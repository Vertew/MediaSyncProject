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

class ChangeModeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private User $user;
    private string $mode;
    private int $room_id;
    private array $shuffle_array;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $mode, int $room_id, array $shuffle_array = array())
    {
        $this->user = $user;
        $this->mode = $mode;
        $this->room_id = $room_id;
        $this->shuffle_array = $shuffle_array;
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
        return 'change-mode';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['username']),
            'newMode' => $this->mode,
            'shuffle_array' => $this->shuffle_array,
        ];
    }
}
