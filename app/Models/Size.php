<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
        'updated_by'
    ];

    public static function boot(){
        parent::boot();

        
    }
}
