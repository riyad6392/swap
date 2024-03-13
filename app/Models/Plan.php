<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'uid',
        'description',
        'price',
        'currency',
        'interval',
        'interval_duration',
        'created_by',
        'updated_by'
    ];
}
