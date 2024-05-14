<?php

namespace App\Traits;

use App\Services\FileUploadService;
use Illuminate\Support\Facades\File;
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
                $model->uid = strtolower(class_basename($model)) .'-'. uniqid();
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

        static::updating(function ($model) {
            if (!$model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public static function bootUserId()
    {
        static::creating(function ($model) {
            if (!$model->isDirty('user_id')) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function fileDetails()
    {
        return [
            'size' => $this->photo_of_id ? FileUploadService::formatSizeUnits(File::size(public_path('storage/' . $this->photo_of_id))) : null,
            'extension' => $this->photo_of_id ? File::extension($this->photo_of_id) : null,
            'basename' => $this->photo_of_id ? File::basename($this->photo_of_id) : null,
            'path' => $this->photo_of_id ? asset('storage/' . $this->photo_of_id) : null
        ];
    }
}
