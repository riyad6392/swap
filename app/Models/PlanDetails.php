<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_id',
        'feature',
        'value',
        'created_by',
        'updated_by'
    ];
}
