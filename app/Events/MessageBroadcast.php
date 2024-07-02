<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
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

    protected Conversation $conversation;
    protected Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Conversation $conversation, Message $message)
    {
        dump($message);
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
        if ($this->conversation->participants->count() > 0) {
            $channels = $this->conversation->participants->filter(function ($participant) {
                return $participant->user_id != auth()->id();
            })->map(function ($participant) {
                return new PrivateChannel('conversation.' . $this->conversation->channel_name . '.' . $participant->user_id);
            });
            return $channels->toArray();
        }
//        return [
//            new PrivateChannel('conversation.'.$this->conversation->channel_name),
//        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageBroadcast';
    }

    public function broadcastWith()
    {
        return [
            'message' => new MessageResource($this->message),
            'conversation' => $this->conversation
        ];
    }
}
