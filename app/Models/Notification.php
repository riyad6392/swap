<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\HasDatabaseNotifications;

class Notification extends Model
{
    use HasFactory, HasDatabaseNotifications;

    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'notifi_by',
        'data',
        'read_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
