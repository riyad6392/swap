<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, CreatedUpdatedBy;
    protected $fillable = [
        'name',
        'uid',
        'stripe_price_id',
        'description',
        'amount',
        'currency',
        'interval',
        'interval_duration',
        'created_by',
        'updated_by'
    ];
    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

    }
}
