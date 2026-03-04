<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatReply extends Model
{
    protected $table = 'chat_replies';
    
    protected $fillable = [
        'message_id',
        'reply_message_id',
    ];

    // Relationships
    public function originalMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function replyMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_message_id');
    }
}
