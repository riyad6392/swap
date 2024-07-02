<?php

namespace App\Wrapper;

use App\Http\Resources\ProductResource;

class ProductResourceWrapper
{
    public $product;
    public $schema;

    public function __construct($product, $schema = null)
    {
        $this->product = $product;
    }

    public function apiWrapper()
    {
        return ProductResource::collection($this->product);

    }

    public function aa()
    {
        $array = [];
        foreach ($this->schema as $item){

        }
    }

}
