<?php

namespace App\Models;

use App\Models\Scopes\UserNotificationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'swap_id',
        'requester_id',
        'exchanger_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function swap(){
        return $this->belongsTo(Swap::class , 'swap_id' , 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserNotificationScope());
    }
}
