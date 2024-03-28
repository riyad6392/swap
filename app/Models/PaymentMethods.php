<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory, ModelAttributeTrait;
    protected $fillable = [
        'method_name',
        'user_id',
        'master_key',
        'master_value',
        'stripe_payment_method_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public static function boot(){
        parent::boot();
        self::bootCreatedUpdatedBy();
    }
    public function scopeUpdatePaymentMethodStatus(Builder $query, $paymentId)
    {
        return $query->where('user_id', auth()->id())
            ->update(['status' => \DB::raw("CASE WHEN stripe_payment_method_id = '{$paymentId}' THEN 'active' ELSE 'inactive' END")]);
    }

}
