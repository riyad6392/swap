<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Swap extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'uid',
        'user_id',
        'requested_user_id',
        'exchanged_user_id',
        'exchanged_user_status',
        'requested_user_status',
        'requested_wholesale_amount',
        'exchanged_wholesale_amount',
        'requested_total_commission',
        'exchanged_total_commission',
        'created_by',
        'updated_by'
    ];

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function exchangeDetails(): HasMany
    {
        return $this->hasMany(SwapExchangeDetails::class);
    }

    public function requestDetail(): HasMany
    {
        return $this->hasMany(SwapRequestDetails::class);
    }

    public function initiateDetails(): HasMany
    {
        return $this->hasMany(SwapInitiateDetails::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exchanged_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exchanged_user_id');
    }

    protected static function boot(): void
    {
        parent::boot();
        self::bootCreatedUpdatedBy();
        self::bootUID();

    }
}
