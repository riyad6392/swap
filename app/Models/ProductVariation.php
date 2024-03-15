<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'price',
        'stock',
        'discount',
        'quantity',
        'discount_type',
        'discount_start_date',
        'discount_end_date'
    ];

    public function image() {
        return $this->belongsTo(Image::class);
    }
}
