<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceForMessage extends JsonResource
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
            'is_active' => $this->is_active,
            'active_at' => $this->active_at,
            'subscription_is_active' => $this->subscription_is_active,
////            'stripe_customer_id' => $this->stripe_customer_id,
////            'is_approved_by_admin' => $this->is_approved_by_admin,
////            'is_super_swapper' => $this->is_super_swapper,
            'image' => new ImageResource($this->image),
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ];
    }
}
