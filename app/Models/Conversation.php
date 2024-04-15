<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'channel_name',
        'user_id',
    ];

    public function participents()
    {
        return $this->hasMany(Participant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
