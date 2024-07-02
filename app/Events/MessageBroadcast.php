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

    protected Conversation $conversation;
    protected $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Conversation $conversation, Message $message)
    {
        $this->conversation = $conversation;
        $this->message = [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message_type' => $message->message_type,
            'swap_id' => $message->swap_id,
            'is_read' => $message->is_read,
            'is_deleted' => $message->is_deleted,
            'file_path' => $message->file_path,
            'type' => $message->type,
            'message' => $message->message,
            'data' => $message->data,
            'sender' => new UserResourceForMessage($message->sender),
            'receiver' => new UserResourceForMessage($message->receiver),
            'swap' => $message->swap ? new SwapResource($message->swap) : null,

        ];
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
                // info('conversation.' . $this->conversation->channel_name . '.' . $participant->user_id);
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
            'message' => $this->message,
            'conversation' => $this->conversation,
        ];
    }
}
