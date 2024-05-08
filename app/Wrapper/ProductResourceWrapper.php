<?php

namespace App\Wrapper;

use App\Http\Resources\ProductResource;

class ProductResourceWrapper
{
    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function apiWrapper()
    {
        return ProductResource::collection($this->product);

    }

}
