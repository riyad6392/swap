<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swap extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'uid',
        'user_id',
        'requested_user_id',
        'exchanged_user_id',
        'status',
        'requested_wholesale_amount',
        'exchanged_wholesale_amount',
        'requested_total_commission',
        'exchanged_total_commission',
        'created_by',
        'updated_by'
    ];

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function exchangeDetails()
    {
        return $this->hasMany(SwapExchangeDetails::class);
    }

    public function requestDetail()
    {
        return $this->hasMany(SwapRequestDetails::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }


    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
        self::bootUID();

    }
}
