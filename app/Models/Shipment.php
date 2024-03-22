<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'swap_id',
        'requested_address',
        'requested_tracking_number',
        'requested_carrier_name',
        'requested_carrier_contact',
        'requested_expected_delivery_date',
        'exchanged_address',
        'exchanged_tracking_number',
        'exchanged_carrier_name',
        'exchanged_carrier_contact',
        'exchanged_expected_delivery_date',
        'created_by',
        'updated_by'
    ];
    protected static function boot()
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
    }
}
