<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;

    /**
     * Create a new event instance.
     */
    public function __construct(User $request)
    {
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.2"),
//            new Channel('testing-channel'),
        ];
    }

    public function broadcastAs() :string
    {
        return 'MessageBroadcast';
    }

    public function broadcastWith()
    {
// this is what we are sending
        return [
            'message' => 'You have a Request',
            'request' => $this->request
        ];
    }
}
