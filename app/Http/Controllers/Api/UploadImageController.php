<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; // Import the Storage facade

class UploadImageController extends Controller
{
    public function uploadPDS(Request $request)
    {
        try {
            // Validate image input
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);
    
            // Store the image in the public directory
            $path = $request->file('image')->store('public/pds_images');
    
            // Log the path to check if it's being stored correctly
            Log::info('Image stored at: ' . $path);
    
            // Optional: generate a public URL
            $url = Storage::url($path);
    
            return response()->json([
                'message' => 'Image uploaded successfully.',
                'path' => $path,
                'url' => $url,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in image upload: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload image'], 500);
        }

}
}
