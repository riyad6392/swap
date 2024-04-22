<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ModelAttributeTrait
{
    public static function bootCreatedUpdatedBy()
    {
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->id();
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public static function bootUID()
    {
        static::creating(function ($model) {
            if (!$model->isDirty('uid')) {
                $model->uid = uniqid();
            }
        });
    }

    public static function bootSlug()
    {
        static::creating(function ($model) {
            if (!$model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
