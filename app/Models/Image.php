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
    protected $appends = [
        'image_path'
    ];

    public function getImagePathAttribute()
    {
//        dd(asset('storage/'.$this->path));
        return env('APP_URL').'/storage/'.$this->path;
    }

    public function imageable()
    {
        return $this->morphTo();
    }
    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

    }
}
