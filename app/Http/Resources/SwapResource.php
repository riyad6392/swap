<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SwapResource extends JsonResource
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
            'uid' => $this->uid,
            'swap_id' => $this->swap_id,
            'exchanged_user_status' => $this->exchanged_user_status,
            'requested_user_status' => $this->requested_user_status,
            'user' => new UserResource($this->user),
            'initiateDetails' => SwapInitiateDetailsResource::collection($this->initiateDetails),
            'created_at' => $this->created_at,
            'exchangeDetails' => SwapExchangeDetailsResource::collection($this->exchangeDetails),
        ];
    }
}
