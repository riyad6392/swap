<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanDetails extends Model
{
    use HasFactory, CreatedUpdatedBy;
    protected $fillable = [
        'plan_id',
        'feature',
        'value',
        'created_by',
        'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();

    }
}
