<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Http\Resources\SwapResource;
use App\Http\Resources\UserResourceForMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageBroadcast implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected  $conversation;
    protected $message;

    /**
     * Create a new event instance.
     */
    public function __construct($conversation, $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.'.$this->conversation->channel_name),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageBroadcast';
    }

    public function broadcastWith()
    {
        $this->message['sender'] = new UserResourceForMessage($this->message->load('sender'));
        $this->message['receiver'] = new UserResourceForMessage($this->message->load('receiver'));
        return [
            'message' => $this->message,
            'conversation' => $this->conversation,
        ];
    }
}
