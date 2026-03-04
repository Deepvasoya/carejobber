<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatAttachment extends Model
{
    protected $table = 'chat_attachments';
    
    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'thumbnail_path',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    // Helper Methods
    public function getFileUrl()
    {
        return Storage::url($this->file_path);
    }

    public function getThumbnailUrl()
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }
        return $this->getFileUrl();
    }

    public function isImage()
    {
        return in_array($this->file_type, ['image', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function isDocument()
    {
        return in_array($this->file_type, ['document', 'doc', 'docx', 'pdf', 'txt']);
    }

    public function isPdf()
    {
        return $this->file_type === 'pdf' || $this->mime_type === 'application/pdf';
    }

    public function getFormattedFileSize()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function deleteFile()
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
        if ($this->thumbnail_path && Storage::exists($this->thumbnail_path)) {
            Storage::delete($this->thumbnail_path);
        }
    }
}

