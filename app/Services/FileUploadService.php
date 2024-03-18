<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public const IMAGE_UPLOAD_PATH = 'images';
    public const FILE_STORAGE = 'public';
    public static function uploadFile(array $requesteImages, Product|ProductVariation $model, array $deleted_image_ids = [], string $upload_path = self::IMAGE_UPLOAD_PATH)
    {
        $imagePath = [];
        
        if ($deleted_image_ids) {
            self::deleteImages($deleted_image_ids, $model);
        }

        foreach ($requesteImages as $imageData) {
            $filename = time() . '-' . uniqid() . '.' . $imageData->getClientOriginalExtension();
            $path = Storage::disk(self::FILE_STORAGE)->putFileAs($upload_path, $imageData, $filename);
            $model->images()->create([
                'path' => $path
            ]);
            $imagePath[$filename] = $path;

        }

        return $imagePath;
    }

    public static function deleteImages(array $deleted_image_ids, Product|ProductVariation $model)
    {
        $images = Image::whereIn('id', $deleted_image_ids)->get();
        foreach ($images as $image) {
            Storage::disk(self::FILE_STORAGE)->delete($image->path);
            $image->delete();
        }

        return $images;
    }
}
