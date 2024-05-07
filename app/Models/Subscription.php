<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory, ModelAttributeTrait;
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'start_date',
        'end_date',
        'amount',
        'payment_method_id',
        'stripe_subscription_id',
        'created_by',
        'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
        self::bootUID();

    }

    public function paymentMethods()
    {
        return $this->belongsTo(PaymentMethods::class);
    }
}
