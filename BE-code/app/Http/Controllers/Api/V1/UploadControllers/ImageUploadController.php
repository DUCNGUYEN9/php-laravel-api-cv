<?php

namespace App\Http\Controllers\Api\V1\UploadControllers;

use Exception;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * upload Or Update images
     */
    public function uploadOrUpdate(Request $request, $user_id)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'user_id' => 'required|exists:users,id|unique:images,user_id',
            ]);

            // Process image upload
            $path = $request->file('image')->store('public/images');

            // Find the image record by user_id
            $image = Image::where('user_id', $user_id)->first();

            // If an image record is found, update existing image
            if ($image) {
                // Delete the previous image file (optional)
                if (Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }

                // Update the path of the existing image
                $image->path = $path;
            } else {
                // Otherwise, create a new image record
                $image = new Image();
                $image->path = $path;
                $image->user_id = $user_id;
            }

            // Save image record
            $image->save();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => $image->wasRecentlyCreated ? 'Image uploaded successfully' : 'Image updated successfully',
                'path' => $path,
            ]);
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading or updating the image',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * show images
     */
    public function show($user_id)
    {
        // Tìm hình ảnh dựa trên user_id
        $image = Image::where('user_id', $user_id)->first();

        // Kiểm tra xem hình ảnh có tồn tại không
        if (!$image) {
            return response()->json([
                'status' => 'error',
                'message' => 'No image found for the specified user'
            ], 404);
        }

        // Trả về hình ảnh nếu tìm thấy
        return response()->json($image);
    }
}
