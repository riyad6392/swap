<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'message_type',
        'swap_id',
        'is_read',
        'is_deleted',
        'file_path',
        'type',
        'message',
        'data',

    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->select('id', 'name');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function swap()
    {
        return $this->belongsTo(Swap::class);
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
    protected function getFilePathAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
