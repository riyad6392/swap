<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResources extends JsonResource
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
            'last_message' => $this->last_message,
            'last_message_id' => $this->last_message_id,
            'name' => $this->name,
            'channel_name' => $this->channel_name,
            'user_id' => $this->user_id,
            'conversation_type' => $this->conversation_type,
            'composite_id' => $this->composite_id,
            'reverse_composite_id' => $this->reverse_composite_id,
            'participants' => ParticipantsResources::collection($this->participants),
        ];
    }
}
