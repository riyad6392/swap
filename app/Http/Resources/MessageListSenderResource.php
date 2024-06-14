<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageListSenderResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'subscription_is_active' => $this->subscription_is_active,
            'stripe_customer_id' => $this->stripe_customer_id,
            'image' => new ImageResource($this->image),
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
        ];
    }
}
