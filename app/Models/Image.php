<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, ModelAttributeTrait;
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'created_by',
        'updated_by',
        'path'
    ];

//    protected $hidden = [
//        'created_at',
//        'updated_at',
//        'imageable_type',
//        'imageable_id',
//        'created_by',
//        'updated_by'
//    ];
    public function getImagePathAttribute()
    {
        return asset($this->path);
    }

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

    }
}
