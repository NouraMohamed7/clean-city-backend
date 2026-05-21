<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadImageTrait
{
    /**
     * Upload image to storage
     */
    protected function uploadImage($image, string $folder = 'images'): string
    {
        $filename = Str::random(20) . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs("public/{$folder}", $filename);

        return Storage::url($path);
    }

    /**
     * Upload multiple images
     */
    protected function uploadImages(array $images, string $folder = 'images'): array
    {
        $paths = [];

        foreach ($images as $image) {
            $paths[] = $this->uploadImage($image, $folder);
        }

        return $paths;
    }

    /**
     * Delete image from storage
     */
    protected function deleteImage(string $path): bool
    {
        if (Storage::exists($path)) {
            return Storage::delete($path);
        }

        return false;
    }
}
