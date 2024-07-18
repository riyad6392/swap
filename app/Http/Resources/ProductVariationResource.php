<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'discount_end_date' => $this->discount_end_date,
//            'product' => $this->product,
            'unit_price' => $this->unit_price,
            'size' => new SizeResource($this->whenLoaded('size')),
            'color' => new ColorResource($this->whenLoaded('color')),
            'images' => ImageResource::collection($this->images),
        ];
    }
}
