<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public const IMAGE_UPLOAD_PATH = 'images';
    public const FILE_STORAGE = 'public';

    public static function uploadFile(array $requestImages, Product|ProductVariation $model, string $upload_path = null): array
    {
        $upload_path = $upload_path ?? strtolower(class_basename($model));
        $imagePath = [];

        foreach ($requestImages as $imageData) {
            $filename = time() . '-' . uniqid() . '.' . $imageData->getClientOriginalExtension();
            $path = Storage::disk(self::FILE_STORAGE)->putFileAs($upload_path, $imageData, $filename);
            $model->images()->create([
                'path' => $path
            ]);
            $imagePath[$filename] = $path;
        }
        return $imagePath;
    }

    public static function deleteImages(array $deleted_image_ids)
    {
        $images = Image::whereIn('id', $deleted_image_ids)->get();
        foreach ($images as $image) {
            Storage::disk(self::FILE_STORAGE)->delete($image->path);
            $image->delete();
        }
        return $images;
    }
}
