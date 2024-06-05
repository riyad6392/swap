<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantsResources extends JsonResource
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
            'user_id' => $this->first_name,
            'conversation_id' => $this->last_name,
            'created_at' => $this->subscription_is_active,
            'updated_at' => $this->stripe_customer_id,
            'user' => new UserResourceForMessage($this->user),
        ];
    }
}
