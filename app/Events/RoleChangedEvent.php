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
use App\Models\Role;

class RoleChangedEvent implements ShouldBroadcast
{

    private User $user;
    private Role $role;
    private int $room_id;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, int $room_id, Role $role)
    {
        $this->user = $user;
        $this->room_id = $room_id;
        $this->role = $role; 
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
        return 'role-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['username']),
            'role' => $this->role->only(['id', 'role'])
        ];
    }

}
