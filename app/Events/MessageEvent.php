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

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $message;
    private User $user;
    private int $room_id;

    /**
     * Create a new event instance.
     */
    public function __construct(string $message, User $user, int $room_id)
    {
        $this->message = $message;
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
        return 'message-sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'user' => $this->user->only(['username','email']),
            'name' => $this->user->profile?->name ?? $this->user->username,
        ];
    }

}
