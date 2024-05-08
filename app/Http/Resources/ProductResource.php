<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => new CategoryResource($this->category),
            'brand' => new BrandResource($this->brand),
            'description' => $this->description,
            'is_publish' => $this->is_publish,
            'image' => new ImageResource($this->image),
            'productVariations' => ProductVariationResource::collection($this->productVariations),
        ];
    }

    public function nestedCollection($schema)
    {
        $data = [];
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $this->$key = $this->nestedCollection($value);
            }
            $data[$key] = $this->$key;
        }
        return $data;
    }
}
