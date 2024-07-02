<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory,ModelAttributeTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'created_by',
        'updated_by',
        'is_published'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function boot(): void
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

        self::bootSlug();
    }
}
