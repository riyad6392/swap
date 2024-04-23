<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, ModelAttributeTrait;
    protected $fillable = [
        'name',
        'uid',
        'stripe_price_id',
        'description',
        'short_description',
        'amount',
        'currency',
        'interval',
        'interval_duration',
        'is_active',
        'plan_type',
        'created_by',
        'updated_by'
    ];

    public function planDetails()
    {
        return $this->hasMany(PlanDetails::class);
    }
    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
        self::bootUID();

    }
}
