<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationBroadcast implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public Conversation $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->conversation->participants->count() > 0) {
            $channels = $this->conversation->participants->filter(function ($participant) {
                return $participant->user_id != auth()->id();
            })->map(function ($participant) {
                return new PrivateChannel('conversation.user.' . $participant->user_id);
            });
            return $channels->toArray();
        }
    }

    public function broadcastAs(): string
    {
        return 'ConversationBroadcast';
    }

    public function broadcastWith()
    {
        return [
            'conversation' => $this->conversation
        ];
    }
}
