<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUpload {
    public function uploadImage($request, $field, $folder)
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Ensure the folder exists in storage/app/public/{folder}
        $folderPath = storage_path('app/public/' . $folder);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Store in storage/app/public/{folder}
        $path = $file->storeAs($folder, $filename, 'public');

        // Return the path (will be converted to URL in Resource)
        return $path;
    }

    public function updateImage($request, $field, $folder, $oldImagePath = null)
    {
        // Delete old image if it exists
        if ($oldImagePath) {
            // Extract path from URL if it's a full URL
            $path = $oldImagePath;
            if (filter_var($oldImagePath, FILTER_VALIDATE_URL)) {
                // Extract path from URL (e.g., http://domain/storage/users/image.jpg -> users/image.jpg)
                $path = str_replace(asset('storage/'), '', $oldImagePath);
            }
            
            // Delete from storage
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Upload new image
        return $this->uploadImage($request, $field, $folder);
    }
    public function deleteImage($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}