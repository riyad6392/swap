<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory, CreatedUpdatedBy;
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
        'discount_end_date',
        'created_by',
        'updated_by'
    ];


    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
    }
}
