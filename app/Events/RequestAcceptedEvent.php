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

class RequestAcceptedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $acceptee;

    /**
     * Create a new event instance.
     */
    public function __construct(int $id, User $acceptee)
    {
        $this->id = $id;
        $this->acceptee = $acceptee;
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
        return 'request-accepted';
    }

    public function broadcastWith(): array
    {
        return [
            'acceptee' => $this->acceptee->username,
        ];
    }
}
