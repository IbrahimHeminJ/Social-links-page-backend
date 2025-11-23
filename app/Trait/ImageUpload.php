<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;

trait ImageUpload {
    public function uploadImage($request, $field, $folder)
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store in storage/app/public/{folder}
        $path = $file->storeAs($folder, $filename, 'public');

        return $path; 
    }

    public function updateImage($request, $field, $folder, $oldImagePath = null)
    {
        // Delete old image if it exists
        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        // Upload new image
        return $this->uploadImage($request, $field, $folder);
    }
}