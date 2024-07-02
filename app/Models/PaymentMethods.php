<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory, ModelAttributeTrait;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'method_name',
        'user_id',
        'master_key',
        'master_value',
        'stripe_payment_method_id',
        'card_brand',
        'card_display_brand',
        'card_last_four',
        'card_exp_month',
        'card_exp_year',
        'card_country',
        'card_funding',
        'payment_type',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
//        static::updatePaymentMethodStatus();

    }

//    public function scopeUpdatePaymentMethodStatus(Builder $query, $paymentId = null)
//    {
//        return $query->where('user_id', auth()->id())
//            ->update(['status' => \DB::raw("CASE WHEN stripe_payment_method_id = '{$paymentId}' THEN '" . self::STATUS_ACTIVE . "' ELSE '" . self::STATUS_INACTIVE . "' END")]);
//    }

}
