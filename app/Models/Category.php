<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'name',
        'status',
        'is_published',
    ];

    public static function boot(): void
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

        self::bootSlug();
    }
}
