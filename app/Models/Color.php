<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory,ModelAttributeTrait;

    protected $fillable = [
        'name',
        'color_code',
        'created_by',
        'updated_by',
        'is_published',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

        self::bootSlug();
    }
}
