<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_type',
        'subscription_id',
        'plan_id',
        'payment_method_id',
        'stripe_payment_intent_id',
        'amount'
    ];
}
