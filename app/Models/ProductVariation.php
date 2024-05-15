<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory,ModelAttributeTrait;
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'unit_price',
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
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
    }
}
