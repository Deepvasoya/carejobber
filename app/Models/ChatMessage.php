<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'is_read',
        'read_at',
        'is_deleted',
        'deleted_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_deleted' => 'boolean',
        'read_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function attachments()
    {
        return $this->hasMany(ChatAttachment::class, 'message_id');
    }

    public function reactions()
    {
        return $this->hasMany(ChatReaction::class, 'message_id');
    }

    public function replyTo()
    {
        return $this->hasOne(ChatReply::class, 'reply_message_id');
    }

    public function replies()
    {
        return $this->hasMany(ChatReply::class, 'message_id');
    }

    // Helper Methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function softDelete()
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);
    }

    public function isImage()
    {
        return $this->message_type === 'image';
    }

    public function isFile()
    {
        return in_array($this->message_type, ['file', 'image']);
    }
}

