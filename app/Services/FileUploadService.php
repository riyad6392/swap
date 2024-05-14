<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;

class FileUploadService
{
    public const IMAGE_UPLOAD_PATH = 'images';
    public const FILE_STORAGE = 'public';


    public static function uploadFile($requestFile, Product|ProductVariation|User $model, string $upload_path = null,): string
    {
        $upload_path = $upload_path ?? strtolower(class_basename($model));
        $filename = time() . '-' . uniqid() . '.' . $requestFile->getClientOriginalExtension();
        return Storage::putFileAs($upload_path, $requestFile, $filename);
    }

    public static function uploadImage($requestImages, Product|ProductVariation|User $model, string $relation = null, string $upload_path = null,): array|string
    {
        $upload_path = $upload_path ?? strtolower(class_basename($model));
        $relation = $relation ?? 'images';
        $imagePath = [];

        if (is_array($requestImages)) {

            foreach ($requestImages as $imageData) {
                $imagePath[] = (new FileUploadService)->manageStore($imageData, $model, $relation, $upload_path,);
            }
        } else {
            $imagePath = (new FileUploadService)->manageStore($requestImages, $model, $relation, $upload_path,);
        }
        return $imagePath;
    }

    protected function manageStore($imageData, $model, $relation, $upload_path,): bool|string
    {
        $filename = time() . '-' . uniqid() . '.' . $imageData->getClientOriginalExtension();
        $path = Storage::putFileAs($upload_path, $imageData, $filename);
        $model->$relation()->create([
            'path' => $path
        ]);
        return $path;
    }

    public static function deleteImages(array $deleted_image_ids, $model, string $relation = null,): bool
    {
        $relation = $relation ?? 'images';
        $images = $model->$relation()
            ->whereIn('id', $deleted_image_ids)
            ->get();

        foreach ($images as $image) {
            Storage::delete($image->path);
            $image->delete();
        }
        return true;
    }

    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }

        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
