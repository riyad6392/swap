<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'path'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'imageable_type',
        'imageable_id'
    ];
    public function getPathAttribute($value)
    {
        return asset($value);
    }
}
