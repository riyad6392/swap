<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'sender_id',
        'receiver_id',
        'swap_id',
        'is_read',
        'is_deleted',
        'file_path',
        'type',
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function swap()
    {
        return $this->belongsTo(Swap::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }


}
