<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChatUserStatus;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatStatusController extends Controller
{
    protected $presenceService;

    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,away,busy,offline',
        ]);

        try {
            $user = Auth::user();
            $userType = 'user';

            $status = ChatUserStatus::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'user_type' => $userType,
                ],
                [
                    'status' => $request->status,
                    'last_activity_at' => now(),
                ]
            );

            if ($request->status === 'online' || $request->status === 'offline') {
                $status->update(['last_seen_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update activity (called on user interaction)
     */
    public function updateActivity()
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }
            
            $userId = $user ? $user->id : $company->id;
            $userType = $user ? 'user' : 'company';

            // Update activity without triggering events
            $status = ChatUserStatus::withoutEvents(function () use ($userId, $userType) {
                return ChatUserStatus::updateOrCreate(
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
            });

            // Cache status for quick access
            Cache::put("chat_status_{$userType}_{$userId}", $status->status, 300);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $status->id,
                    'user_id' => $status->user_id,
                    'user_type' => $status->user_type,
                    'status' => $status->status,
                    'last_activity_at' => $status->last_activity_at,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Chat status update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user status
     */
    public function getStatus($userId, $userType)
    {
        try {
            $status = ChatUserStatus::where('user_id', $userId)
                ->where('user_type', $userType)
                ->first();

            if (!$status) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'offline',
                        'last_seen_at' => null,
                        'last_activity_at' => null,
                    ],
                ]);
            }

            // Determine actual online status based on last activity
            $actualStatus = 'offline';
            if ($status->last_activity_at) {
                $minutesSinceActivity = $status->last_activity_at->diffInMinutes(now());
                // User is online only if status is 'online' AND active within last 5 minutes
                if ($status->status === 'online' && $minutesSinceActivity <= 5) {
                    $actualStatus = 'online';
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $status->id,
                    'user_id' => $status->user_id,
                    'user_type' => $status->user_type,
                    'status' => $actualStatus,
                    'last_seen_at' => $status->last_seen_at ? $status->last_seen_at->format('Y-m-d\TH:i:s\Z') : null,
                    'last_activity_at' => $status->last_activity_at ? $status->last_activity_at->format('Y-m-d\TH:i:s\Z') : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update typing indicator
     */
    public function updateTyping(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'is_typing' => 'required|boolean',
        ]);

        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $userId = $user ? $user->id : $company->id;
            $userType = $user ? 'user' : 'company';

            $status = ChatUserStatus::updateOrCreate(
                [
                    'user_id' => $userId,
                    'user_type' => $userType,
                ],
                [
                    'status' => 'online',
                    'last_activity_at' => now(),
                ]
            );

            $status->setTyping($request->conversation_id, $request->is_typing);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Chat typing update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'conversation_id' => $request->conversation_id ?? null,
                'user_id' => $userId ?? null,
                'user_type' => $userType ?? null,
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : 'An error occurred',
            ], 500);
        }
    }

    /**
     * Get typing status for a conversation
     */
    public function getTypingStatus($conversationId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $userId = $user ? $user->id : $company->id;
            $userType = $user ? 'user' : 'company';

            // Get the other participant's typing status
            $conversation = \App\Models\ChatConversation::findOrFail($conversationId);
            $otherUserId = $conversation->user_id === $userId ? $conversation->company_id : $conversation->user_id;
            $otherUserType = $conversation->user_id === $userId ? 'company' : 'user';

            $status = ChatUserStatus::where('user_id', $otherUserId)
                ->where('user_type', $otherUserType)
                ->where('is_typing', true)
                ->where('typing_conversation_id', $conversationId)
                ->first();

            if ($status) {
                // Get user/company name
                if ($otherUserType === 'user') {
                    $otherUser = \App\User::find($otherUserId);
                    $name = $otherUser ? ($otherUser->name ?? 'User') : 'Someone';
                } else {
                    $otherCompany = \App\Company::find($otherUserId);
                    $name = $otherCompany ? ($otherCompany->name ?? 'Company') : 'Someone';
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'is_typing' => true,
                        'user' => [
                            'id' => $otherUserId,
                            'type' => $otherUserType,
                            'name' => $name,
                        ],
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'is_typing' => false,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

