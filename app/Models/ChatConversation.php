<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Company;
use App\User;

class ChatConversation extends Model
{
    protected $table = 'chat_conversations';
    
    protected $fillable = [
        'company_id',
        'user_id',
        'type',
        'last_message_id',
        'last_message_at',
        'unread_count_company',
        'unread_count_user',
        'is_archived_company',
        'is_archived_user',
    ];

    protected $casts = [
        'is_archived_company' => 'boolean',
        'is_archived_user' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'last_message_id');
    }

    // Helper Methods
    public function getOtherParticipant($currentUserId, $currentUserType)
    {
        if ($currentUserType === 'company') {
            return $this->user;
        }
        return $this->company;
    }

    public function incrementUnreadCount($userType)
    {
        if ($userType === 'company') {
            $this->increment('unread_count_company');
        } else {
            $this->increment('unread_count_user');
        }
    }

    public function resetUnreadCount($userType)
    {
        if ($userType === 'company') {
            $this->update(['unread_count_company' => 0]);
        } else {
            $this->update(['unread_count_user' => 0]);
        }
    }
}

