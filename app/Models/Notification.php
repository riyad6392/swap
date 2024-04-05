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
        'type',
        'swap_id',
        'requester_id',
        'exchanger_id',
        'data',
        'read_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function swap(){
        return $this->belongsTo(Swap::class , 'swap_id' , 'id');
    }

    public static function boot()
    {
        static::addGlobalScope(new UserNotificationScope());
    }
}
