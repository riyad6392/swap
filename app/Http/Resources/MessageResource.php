<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message_type' => $this->message_type,
            'swap_id' => $this->swap_id,
            'is_read' => $this->is_read,
            'is_deleted' => $this->is_deleted,
            'file_path' => $this->file_path,
            'type' => $this->type,
            'message' => $this->message,
            'data' => $this->data,
            'last_seen_users' => UserResourceForMessage::collection($this->last_seen_users ?? []),
            /*conversation->participants->filter(function ($participant) {
                return $participant->message_id == $this->id;
            })->map(function ($participant) {
                return [
                    $participant->user,
                ];
            }),*/
            'sender' => new UserResourceForMessage($this->sender),
            'receiver' => new UserResourceForMessage($this->receiver),
            'swap' => new SwapResource($this->swap),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            //'conversation' => new ConversationResource($this->conversation),
        ];
    }
}
