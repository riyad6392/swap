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
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at,
            'exchangeDetails' => SwapExchangeDetailsResource::collection($this->exchangeDetails),
        ];
    }
}
