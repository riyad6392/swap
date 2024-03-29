<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapExchangeDetails extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'swap_id',
        'user_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'unit_price',
        'amount',
        'commission',
        'created_by',
        'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
        self::bootUID();
    }
}
