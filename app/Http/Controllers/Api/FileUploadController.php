<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadController extends BaseController
{
    /**
     * Upload user profile image
     */
    public function uploadProfileImage(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return $this->sendError('Unauthorized', [], 401);
            }

            // Debug: Log what we're receiving
            \Log::info('Upload request data:', [
                'has_image' => $request->has('image'),
                'image_type' => gettype($request->image),
                'image_start' => is_string($request->image) ? substr($request->image, 0, 50) : 'not_string',
                'is_base64' => is_string($request->image) && strpos($request->image, 'data:image') === 0,
                'request_keys' => array_keys($request->all())
            ]);

            // Check if it's a base64 image or file upload
            if ($request->has('image') && is_string($request->image)) {
                // Handle base64 image
                $imageData = $request->image;
                
                // Remove data URL prefix if present
                if (strpos($imageData, 'data:image') === 0) {
                    $imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $imageData);
                }
                
                try {
                    $decodedImage = base64_decode($imageData);
                    if ($decodedImage === false) {
                        \Log::error('Failed to decode base64 image data');
                        return $this->sendError('Failed to decode image data', [], 422);
                    }
                    
                    $imageInfo = getimagesizefromstring($decodedImage);
                    if ($imageInfo === false) {
                        \Log::error('Invalid image data - getimagesizefromstring failed');
                        return $this->sendError('Invalid image data', [], 422);
                    }
                    
                    \Log::info('Image info:', $imageInfo);
                } catch (\Exception $e) {
                    \Log::error('Error processing base64 image: ' . $e->getMessage());
                    return $this->sendError('Error processing image: ' . $e->getMessage(), [], 422);
                }
                
                $mimeType = $imageInfo['mime'];
                $extension = explode('/', $mimeType)[1];
                $filename = time() . '_' . Str::random(10) . '.' . $extension;
                
                // Use the already decoded image data
                $imageData = $decodedImage;
                
                // Create different sizes
                $image = Image::make($imageData);
                
                // Ensure directories exist
                $userImagesDir = public_path('user_images');
                $midDir = public_path('user_images/mid');
                $thumbDir = public_path('user_images/thumb');
                
                if (!file_exists($userImagesDir)) {
                    mkdir($userImagesDir, 0755, true);
                }
                if (!file_exists($midDir)) {
                    mkdir($midDir, 0755, true);
                }
                if (!file_exists($thumbDir)) {
                    mkdir($thumbDir, 0755, true);
                }
                
                // Save original
                file_put_contents($userImagesDir . '/' . $filename, $imageData);
                
                // Create mid size (300x300)
                $image->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($midDir . '/' . $filename);
                
                // Create thumb size (100x100)
                $image->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($thumbDir . '/' . $filename);
                
                // Update user profile
                $user->image = $filename;
                $user->save();
                
                return $this->sendResponse([
                    'filename' => $filename,
                    'url' => asset('user_images/' . $filename),
                    'thumb_url' => asset('user_images/thumb/' . $filename),
                    'mid_url' => asset('user_images/mid/' . $filename)
                ], 'Profile image uploaded successfully');
                
            } elseif ($request->hasFile('image')) {
                // Handle file upload (original method)
                $validator = Validator::make($request->all(), [
                    'image' => 'required|image|max:2048',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation error', $validator->errors(), 422);
                }
            } else {
                // If neither base64 nor file, return error
                \Log::error('No valid image data found in request');
                return $this->sendError('No valid image data found', [], 422);
            }
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}