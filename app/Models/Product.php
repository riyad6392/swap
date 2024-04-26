<?php

namespace App\Models;

use App\Models\Scopes\UserSpecificDataScope;
use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,ModelAttributeTrait;

    protected $fillable = [
        'name',
        'category_id',
        'user_id',
        'description',
        'brand_id',
        'is_publish',
        'created_by',
        'updated_by'
    ];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function category()
    {
//        return $this->belongsTo(Category::class);
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

        self::bootUserId();
        static::addGlobalScope(new UserSpecificDataScope());
    }

}
