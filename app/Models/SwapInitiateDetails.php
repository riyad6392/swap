<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapInitiateDetails extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'swap_id',
        'uid',
        'user_id',
        'product_id',
        'created_by',
        'updated_by',
    ];


    public static function boot()
    {
        parent::boot();
        parent::bootCreatedUpdatedBy();
        parent::bootUID();
    }
}
