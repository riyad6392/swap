<?php

namespace App\Models\Scopes;

use App\Models\Swap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

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

    public function swap()
    {
        return $this->belongsTo(Swap::class, 'data.swap_id', 'id');
    }
}
