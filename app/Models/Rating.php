<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'rated_id', 'rating', 'comments'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_id');
    }
}
