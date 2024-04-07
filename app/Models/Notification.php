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
        'notifiable',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserNotificationScope());
    }


}
