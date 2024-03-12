<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'user_id',
        'description',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function category()
    {
//        return $this->belongsTo(Category::class);
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class);
    }


}
