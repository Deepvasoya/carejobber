<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\UserStatusChanged;

class ChatUserStatus extends Model
{
    protected $table = 'chat_user_status';
    
    protected $fillable = [
        'user_id',
        'user_type',
        'status',
        'last_seen_at',
        'last_activity_at',
        'is_typing',
        'typing_conversation_id',
    ];

    protected $casts = [
        'is_typing' => 'boolean',
        'last_seen_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    // Temporarily disabled to prevent errors
    // protected $dispatchesEvents = [
    //     'updated' => UserStatusChanged::class,
    // ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            $model->last_activity_at = now();
            if ($model->status === 'online') {
                $model->last_seen_at = now();
            }
        });
    }

    // Helper Methods
    public function setOnline()
    {
        $this->update([
            'status' => 'online',
            'last_seen_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    public function setAway()
    {
        $this->update(['status' => 'away']);
    }

    public function setBusy()
    {
        $this->update(['status' => 'busy']);
    }

    public function setOffline()
    {
        $this->update([
            'status' => 'offline',
            'last_seen_at' => now(),
        ]);
    }

    public function setTyping($conversationId, $isTyping = true)
    {
        $this->update([
            'is_typing' => $isTyping,
            'typing_conversation_id' => $isTyping ? $conversationId : null,
        ]);
    }
}

