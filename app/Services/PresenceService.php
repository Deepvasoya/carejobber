<?php

namespace App\Services;

use App\Models\ChatUserStatus;
use Illuminate\Support\Facades\Cache;

class PresenceService
{
    /**
     * Update user activity
     */
    public function updateActivity($userId, $userType)
    {
        $status = ChatUserStatus::updateOrCreate(
            [
                'user_id' => $userId,
                'user_type' => $userType,
            ],
            [
                'status' => 'online',
                'last_activity_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        // Cache status for quick access
        Cache::put("chat_status_{$userType}_{$userId}", $status->status, 300);

        return $status;
    }

    /**
     * Get user status
     */
    public function getUserStatus($userId, $userType)
    {
        // Try cache first
        $cachedStatus = Cache::get("chat_status_{$userType}_{$userId}");
        if ($cachedStatus) {
            return $cachedStatus;
        }

        $status = ChatUserStatus::where('user_id', $userId)
            ->where('user_type', $userType)
            ->first();

        if (!$status) {
            return 'offline';
        }

        // Update cache
        Cache::put("chat_status_{$userType}_{$userId}", $status->status, 300);

        return $status->status;
    }

    /**
     * Update users to away status (called by scheduled task)
     */
    public function updateInactiveUsers()
    {
        $fiveMinutesAgo = now()->subMinutes(5);
        
        ChatUserStatus::where('status', 'online')
            ->where('last_activity_at', '<', $fiveMinutesAgo)
            ->update(['status' => 'away']);

        // Clear cache for updated users
        ChatUserStatus::where('status', 'away')
            ->where('last_activity_at', '<', $fiveMinutesAgo)
            ->get()
            ->each(function ($status) {
                Cache::forget("chat_status_{$status->user_type}_{$status->user_id}");
            });
    }
}

