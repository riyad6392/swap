<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanDetails extends Model
{
    use HasFactory, ModelAttributeTrait;
    protected $fillable = [
        'plan_id',
        'feature',
        'features_count',
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
