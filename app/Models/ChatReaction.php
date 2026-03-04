<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatReaction extends Model
{
    protected $table = 'chat_reactions';
    
    protected $fillable = [
        'message_id',
        'user_id',
        'user_type',
        'emoji',
    ];

    // Relationships
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }
}
