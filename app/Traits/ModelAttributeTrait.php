<?php

namespace App\Traits;

use App\Services\FileUploadService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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

    public function fileDetails($data)
    {
        return [
            'size' => $data ? FileUploadService::formatSizeUnits(File::size(public_path('storage/' . $data))) : null,
            'extension' => $data ? File::extension($data) : null,
            'basename' => $data ? File::basename($data) : null,
            'path' => $data ? asset('storage/' . $data) : null,
            'storage_path' => $data ? Storage::url($data) : null
        ];
    }
}
