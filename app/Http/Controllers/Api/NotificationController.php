<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Update user's push token
     */
    public function updatePushToken(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'push_token' => 'required|string|max:255',
                'platform' => 'required|string|in:ios,android',
                'device_id' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pushToken = $request->input('push_token');
            $platform = $request->input('platform');
            $deviceId = $request->input('device_id');

            // Update or create push token record
            DB::table('user_push_tokens')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'device_id' => $deviceId
                ],
                [
                    'push_token' => $pushToken,
                    'platform' => $platform,
                    'device_id' => $deviceId,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            Log::info("Push token updated for user {$user->id}: {$pushToken}");

            return response()->json([
                'success' => true,
                'message' => 'Push token updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating push token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating push token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's notification preferences
     */
    public function getPreferences(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Get user preferences from database
            $preferences = DB::table('user_notification_preferences')
                ->where('user_id', $user->id)
                ->first();

            if (!$preferences) {
                // Return default preferences
                $defaultPreferences = [
                    'new_job_matches' => true,
                    'application_updates' => true,
                    'company_jobs' => true,
                    'job_alerts' => true,
                    'profile_reminders' => true,
                    'messages' => true,
                    'security_alerts' => true,
                    'app_updates' => true,
                    'marketing' => false,
                    'quiet_hours_enabled' => true,
                    'quiet_hours_start' => '22:00',
                    'quiet_hours_end' => '08:00',
                ];

                return response()->json([
                    'success' => true,
                    'data' => $defaultPreferences
                ]);
            }

            // Convert database record to array
            $preferencesArray = [
                'new_job_matches' => (bool) $preferences->new_job_matches,
                'application_updates' => (bool) $preferences->application_updates,
                'company_jobs' => (bool) $preferences->company_jobs,
                'job_alerts' => (bool) $preferences->job_alerts,
                'profile_reminders' => (bool) $preferences->profile_reminders,
                'messages' => (bool) $preferences->messages,
                'security_alerts' => (bool) $preferences->security_alerts,
                'app_updates' => (bool) $preferences->app_updates,
                'marketing' => (bool) $preferences->marketing,
                'quiet_hours_enabled' => (bool) $preferences->quiet_hours_enabled,
                'quiet_hours_start' => $preferences->quiet_hours_start,
                'quiet_hours_end' => $preferences->quiet_hours_end,
            ];

            return response()->json([
                'success' => true,
                'data' => $preferencesArray
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting notification preferences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting notification preferences: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's notification preferences
     */
    public function updatePreferences(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'new_job_matches' => 'boolean',
                'application_updates' => 'boolean',
                'company_jobs' => 'boolean',
                'job_alerts' => 'boolean',
                'profile_reminders' => 'boolean',
                'messages' => 'boolean',
                'security_alerts' => 'boolean',
                'app_updates' => 'boolean',
                'marketing' => 'boolean',
                'quiet_hours_enabled' => 'boolean',
                'quiet_hours_start' => 'string|regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/',
                'quiet_hours_end' => 'string|regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare data for update
            $updateData = [
                'updated_at' => now(),
            ];

            // Add only provided fields
            $allowedFields = [
                'new_job_matches', 'application_updates', 'company_jobs', 'job_alerts',
                'profile_reminders', 'messages', 'security_alerts', 'app_updates',
                'marketing', 'quiet_hours_enabled', 'quiet_hours_start', 'quiet_hours_end'
            ];

            foreach ($allowedFields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            // Update or create preferences
            DB::table('user_notification_preferences')->updateOrInsert(
                ['user_id' => $user->id],
                array_merge($updateData, [
                    'user_id' => $user->id,
                    'created_at' => now()
                ])
            );

            Log::info("Notification preferences updated for user {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating notification preferences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating notification preferences: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send push notification
     */
    public function sendPushNotification(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:500',
                'data' => 'nullable|array',
                'sound' => 'boolean',
                'badge' => 'nullable|integer|min:0',
                'priority' => 'nullable|string|in:min,low,default,high,max',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get user's push tokens
            $pushTokens = DB::table('user_push_tokens')
                ->where('user_id', $user->id)
                ->pluck('push_token')
                ->toArray();

            if (empty($pushTokens)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No push tokens found for user'
                ], 404);
            }

            // Store notification in database
            $notificationId = DB::table('notifications')->insertGetId([
                'user_id' => $user->id,
                'type' => $request->input('type'),
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'data' => json_encode($request->input('data', [])),
                'sound' => $request->input('sound', true),
                'badge' => $request->input('badge'),
                'priority' => $request->input('priority', 'default'),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send push notification via Expo
            $this->sendExpoPushNotification($pushTokens, [
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'data' => array_merge($request->input('data', []), [
                    'notification_id' => $notificationId,
                    'type' => $request->input('type'),
                ]),
                'sound' => $request->input('sound', true) ? 'default' : null,
                'badge' => $request->input('badge'),
                'priority' => $request->input('priority', 'default'),
            ]);

            Log::info("Push notification sent to user {$user->id}: {$request->input('title')}");

            return response()->json([
                'success' => true,
                'message' => 'Push notification sent successfully',
                'notification_id' => $notificationId
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending push notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending push notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send push notification via Expo Push API
     */
    private function sendExpoPushNotification($pushTokens, $notification)
    {
        try {
            $messages = [];
            
            foreach ($pushTokens as $token) {
                $messages[] = [
                    'to' => $token,
                    'title' => $notification['title'],
                    'body' => $notification['body'],
                    'data' => $notification['data'],
                    'sound' => $notification['sound'],
                    'badge' => $notification['badge'],
                    'priority' => $notification['priority'],
                ];
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://exp.host/--/api/v2/push/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messages));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Accept-Encoding: gzip, deflate',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error("Expo push notification failed with HTTP code: {$httpCode}, Response: {$response}");
            } else {
                Log::info("Expo push notification sent successfully: {$response}");
            }

        } catch (\Exception $e) {
            Log::error('Error sending Expo push notification: ' . $e->getMessage());
        }
    }

    /**
     * Get user's notification history
     */
    public function getNotificationHistory(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $page = $request->input('page', 1);
            $limit = $request->input('limit', 20);
            $offset = ($page - 1) * $limit;

            $notifications = DB::table('notifications')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            // Format notifications
            $formattedNotifications = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => json_decode($notification->data, true),
                    'sound' => (bool) $notification->sound,
                    'badge' => $notification->badge,
                    'priority' => $notification->priority,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedNotifications,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => DB::table('notifications')->where('user_id', $user->id)->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting notification history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting notification history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notificationId = $request->input('notification_id');

            // Verify notification belongs to user
            $notification = DB::table('notifications')
                ->where('id', $notificationId)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found or access denied'
                ], 404);
            }

            // Mark as read
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update([
                    'read_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Verify notification belongs to user
            $notification = DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found or access denied'
                ], 404);
            }

            // Delete notification
            DB::table('notifications')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting notification: ' . $e->getMessage()
            ], 500);
        }
    }
} 