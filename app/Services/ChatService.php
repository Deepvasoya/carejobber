<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ChatService
{
    /**
     * Check if company and user can chat.
     * Company must unlock the candidate profile to view details or chat (including applied candidates).
     */
    public function canChat($companyId, $userId)
    {
        $isUnlocked = DB::table('unlocked_user_status')
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->exists();

        return $isUnlocked;
    }

    /**
     * Get or create conversation
     */
    public function getOrCreateConversation($companyId, $userId)
    {
        $conversation = ChatConversation::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            // Determine type
            $hasApplied = DB::table('job_apply')
                ->join('jobs', 'job_apply.job_id', '=', 'jobs.id')
                ->where('jobs.company_id', $companyId)
                ->where('job_apply.user_id', $userId)
                ->exists();

            $type = $hasApplied ? 'applied' : 'unlocked';

            $conversation = ChatConversation::create([
                'company_id' => $companyId,
                'user_id' => $userId,
                'type' => $type,
            ]);
        }

        return $conversation;
    }

    /**
     * Handle file upload for chat message
     */
    public function handleFileUpload($file, $messageId)
    {
        $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $allowedDocTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Determine file type
        $fileType = 'file';
        if (in_array($mimeType, $allowedImageTypes)) {
            $fileType = 'image';
        } elseif (in_array($mimeType, $allowedDocTypes)) {
            $fileType = $extension === 'pdf' ? 'pdf' : 'document';
        }

        // Validate file size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($fileSize > $maxSize) {
            throw new \Exception('File size exceeds maximum limit of 10MB');
        }

        // Create storage path
        $storagePath = 'chat/attachments/' . date('Y/m');
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $filePath = $file->storeAs($storagePath, $fileName, 'public');

        $thumbnailPath = null;

        // Generate thumbnail for images
        if ($fileType === 'image') {
            try {
                $thumbnailPath = $this->generateThumbnail($file, $storagePath);
            } catch (\Exception $e) {
                // If thumbnail generation fails, continue without thumbnail
                \Log::warning('Thumbnail generation failed: ' . $e->getMessage());
            }
        }

        // Create attachment record
        $attachment = ChatAttachment::create([
            'message_id' => $messageId,
            'file_name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'thumbnail_path' => $thumbnailPath,
        ]);

        return $attachment;
    }

    /**
     * Generate thumbnail for image
     */
    private function generateThumbnail($file, $storagePath)
    {
        $image = Image::make($file);
        
        // Resize to max 300x300 while maintaining aspect ratio
        $image->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $thumbnailName = 'thumb_' . time() . '_' . uniqid() . '.jpg';
        $thumbnailPath = $storagePath . '/' . $thumbnailName;

        // Save thumbnail
        Storage::disk('public')->put($thumbnailPath, (string) $image->encode('jpg', 80));

        return $thumbnailPath;
    }

    /**
     * Get file download URL
     */
    public function getFileDownloadUrl($attachmentId)
    {
        $attachment = ChatAttachment::findOrFail($attachmentId);
        return Storage::disk('public')->url($attachment->file_path);
    }
}

