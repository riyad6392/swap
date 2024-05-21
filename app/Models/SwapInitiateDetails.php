<?php

namespace App\Models;

use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwapInitiateDetails extends Model
{
    use HasFactory, ModelAttributeTrait;

    protected $fillable = [
        'swap_id',
        'uid',
        'user_id',
        'product_id',
        'created_by',
        'updated_by',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function swap(): BelongsTo
    {
        return $this->belongsTo(Swap::class);
    }

    public static function boot()
    {
        parent::boot();
        parent::bootCreatedUpdatedBy();
        parent::bootUID();
    }
}
