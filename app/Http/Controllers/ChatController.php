<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatReaction;
use App\Models\ChatReply;
use App\Models\ChatUserStatus;
use App\Services\ChatService;
use App\Mail\ChatMessageNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Show full-page chat interface
     */
    public function index()
    {
        $user = Auth::user();
        $company = Auth::guard('company')->user();
        
        if (!$user && !$company) {
            return redirect()->route('login');
        }

        return view('chat.index');
    }

    /**
     * Get all conversations for current user
     */
    public function getConversations(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unlocked, byjobs, unread
        $jobId = $request->get('job_id', null);
        
        return $this->fetchConversations($filter, $jobId);
    }
    
    /**
     * Get jobs for the company or user (for By Jobs filter)
     */
    public function getJobs(Request $request)
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
            
            $search = $request->get('search', '');
            
            if ($company) {
                // For companies: Get jobs they posted
                $jobs = \App\Job::where('company_id', $company->id)
                    ->where('is_active', 1)
                    ->when($search, function($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'title', 'created_at'])
                    ->map(function($job) use ($company) {
                        // Count conversations with users who applied to this job
                        $appliedUserIds = \App\JobApply::where('job_id', $job->id)
                            ->pluck('user_id')
                            ->toArray();
                        
                        $chatCount = 0;
                        if (!empty($appliedUserIds)) {
                            $chatCount = \App\Models\ChatConversation::where('company_id', $company->id)
                                ->whereIn('user_id', $appliedUserIds)
                                ->count();
                        }
                        
                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'created_at' => $job->created_at->format('M Y'),
                            'chat_count' => $chatCount,
                        ];
                    });
            } else {
                // For users: Get jobs they applied to
                $appliedJobIds = \App\JobApply::where('user_id', $user->id)
                    ->pluck('job_id')
                    ->toArray();
                
                if (empty($appliedJobIds)) {
                    return response()->json([
                        'success' => true,
                        'data' => [],
                    ]);
                }
                
                $jobs = \App\Job::whereIn('id', $appliedJobIds)
                    ->where('is_active', 1)
                    ->when($search, function($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'title', 'created_at', 'company_id'])
                    ->map(function($job) use ($user) {
                        // Count conversations with companies for this job
                        $chatCount = \App\Models\ChatConversation::where('user_id', $user->id)
                            ->where('company_id', $job->company_id)
                            ->count();
                        
                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'created_at' => $job->created_at->format('M Y'),
                            'chat_count' => $chatCount,
                        ];
                    });
            }
            
            return response()->json([
                'success' => true,
                'data' => $jobs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Fetch conversations with filters
     */
    private function fetchConversations($filter = 'all', $jobId = null)
    {
        try {
            // Support both user and company auth
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
            
            // Build query based on who is logged in
            $query = ChatConversation::query();
            if ($user) {
                $query->where('user_id', $user->id);
            } elseif ($company) {
                $query->where('company_id', $company->id);
            }
            
            // Apply filters
            if ($filter === 'unlocked' && $company) {
                // Only show conversations with unlocked users
                $unlockedUserIds = \App\UnlockedUserStatus::where('company_id', $company->id)
                    ->where('status', 'unlocked')
                    ->pluck('user_id')
                    ->toArray();
                $query->whereIn('user_id', $unlockedUserIds);
            } elseif ($filter === 'byjobs' && $jobId) {
                if ($company) {
                    // For companies: Show conversations with users who applied to this job
                    $appliedUserIds = \App\JobApply::where('job_id', $jobId)
                        ->pluck('user_id')
                        ->toArray();
                    $query->whereIn('user_id', $appliedUserIds);
                } elseif ($user) {
                    // For users: Show conversations with companies for this job
                    $job = \App\Job::find($jobId);
                    if ($job) {
                        $query->where('company_id', $job->company_id);
                    } else {
                        // If job not found, return empty result
                        $query->where('id', 0);
                    }
                }
            } elseif ($filter === 'unread') {
                // Only show conversations with unread messages
                if ($user) {
                    $query->where('unread_count_user', '>', 0);
                } elseif ($company) {
                    $query->where('unread_count_company', '>', 0);
                }
            }
            
            $conversations = $query
                ->with(['company', 'user', 'lastMessage'])
                ->orderBy('last_message_at', 'desc')
                ->get()
                ->map(function ($conversation) use ($user, $company) {
                    // Determine the other participant
                    $otherParticipant = $user ? $conversation->company : $conversation->user;
                    
                    // Handle null relationships
                    if (!$otherParticipant) {
                        return null;
                    }
                    
                    $unreadCount = $user ? $conversation->unread_count_user : $conversation->unread_count_company;
                    
                    // Build other participant info
                    $otherParticipantData = [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name ?? ($otherParticipant->getName ? $otherParticipant->getName() : 'Unknown'),
                        'logo' => asset('images/default-user.png'),
                    ];
                    
                    // Set logo based on type
                    if ($user && $conversation->company) {
                        // User viewing company
                        $otherParticipantData['logo'] = $conversation->company->logo ? asset('company_logos/' . $conversation->company->logo) : asset('images/default-company.png');
                        $otherParticipantData['slug'] = $conversation->company->slug ?? null;
                        $otherParticipantData['type'] = 'company';
                    } elseif ($company && $conversation->user) {
                        // Company viewing user
                        $otherParticipantData['logo'] = $conversation->user->image ? asset('user_images/' . $conversation->user->image) : asset('images/default-user.png');
                        $otherParticipantData['type'] = 'user';
                    }
                    
                    // Get participant status
                    $participantType = $user ? 'company' : 'user';
                    $participantStatus = \App\Models\ChatUserStatus::where('user_id', $otherParticipant->id)
                        ->where('user_type', $participantType)
                        ->first();
                    
                    // Determine if user is online (active within last 5 minutes)
                    $isOnline = false;
                    if ($participantStatus) {
                        $lastActivity = $participantStatus->last_activity_at;
                        if ($lastActivity && $lastActivity->diffInMinutes(now()) <= 5) {
                            $isOnline = $participantStatus->status === 'online';
                        }
                    }
                    
                    $otherParticipantData['status'] = $isOnline ? 'online' : 'offline';
                    
                    // Add last_seen_at and last_activity_at to other_participant
                    if ($participantStatus) {
                        $otherParticipantData['last_seen_at'] = $participantStatus->last_seen_at ? $participantStatus->last_seen_at->format('Y-m-d\TH:i:s\Z') : null;
                        $otherParticipantData['last_activity_at'] = $participantStatus->last_activity_at ? $participantStatus->last_activity_at->format('Y-m-d\TH:i:s\Z') : null;
                    }
                    
                    return [
                        'id' => $conversation->id,
                        'company' => $user && $conversation->company ? [
                            'id' => $conversation->company->id,
                            'name' => $conversation->company->name,
                            'logo' => $conversation->company->logo ? asset('company_logos/' . $conversation->company->logo) : asset('images/default-company.png'),
                            'slug' => $conversation->company->slug ?? null,
                        ] : null,
                        'user' => $company && $conversation->user ? [
                            'id' => $conversation->user->id,
                            'name' => $conversation->user->name ?? ($conversation->user->getName ? $conversation->user->getName() : 'Unknown'),
                            'logo' => $conversation->user->image ? asset('user_images/' . $conversation->user->image) : asset('images/default-user.png'),
                        ] : null,
                        'other_participant' => $otherParticipantData,
                        'last_message' => $conversation->lastMessage ? [
                            'message' => $conversation->lastMessage->message,
                            'message_type' => $conversation->lastMessage->message_type,
                            'created_at' => $conversation->lastMessage->created_at,
                        ] : null,
                        'unread_count' => $unreadCount,
                        'last_message_at' => $conversation->last_message_at,
                    ];
                })
                ->filter(function ($conv) {
                    return $conv !== null; // Remove null entries
                })
                ->values(); // Re-index array

            return response()->json([
                'success' => true,
                'data' => $conversations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            $conversation = ChatConversation::findOrFail($conversationId);

            // Verify access
            if ($user && $conversation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if ($company && $conversation->company_id !== $company->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $searchQuery = $request->input('search', '');
            
            $messagesQuery = ChatMessage::where('conversation_id', $conversationId)
                ->where('is_deleted', false);
            
            // Add search filter if query is provided
            if (!empty($searchQuery)) {
                $messagesQuery->where('message', 'like', '%' . $searchQuery . '%');
            }
            
            $messages = $messagesQuery
                ->with(['attachments', 'reactions', 'replyTo.replyMessage'])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) use ($user, $company) {
                    $isOwn = false;
                    if ($user && $message->sender_id === $user->id && $message->sender_type === 'user') {
                        $isOwn = true;
                    } elseif ($company && $message->sender_id === $company->id && $message->sender_type === 'company') {
                        $isOwn = true;
                    }
                    
                    // Group reactions by emoji
                    $reactionsGrouped = $message->reactions->groupBy('emoji')->map(function ($reactions, $emoji) {
                        return [
                            'emoji' => $emoji,
                            'count' => $reactions->count(),
                            'users' => $reactions->map(function ($reaction) {
                                return [
                                    'id' => $reaction->user_id,
                                    'type' => $reaction->user_type,
                                ];
                            })->toArray(),
                        ];
                    })->values();
                    
                    // Get reply info if this message is a reply
                    $replyInfo = null;
                    if ($message->replyTo) {
                        $originalMsg = $message->replyTo->originalMessage;
                        if ($originalMsg) {
                            $replyInfo = [
                                'message_id' => $originalMsg->id,
                                'message_preview' => $originalMsg->message_type === 'text' 
                                    ? (mb_strlen($originalMsg->message) > 50 ? mb_substr($originalMsg->message, 0, 50) . '...' : $originalMsg->message)
                                    : ($originalMsg->message_type === 'image' ? '📷 Image' : '📎 File'),
                            ];
                        }
                    }
                    
                    return [
                        'id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'sender_type' => $message->sender_type,
                        'message' => $message->message,
                        'message_type' => $message->message_type,
                        'is_read' => $message->is_read,
                        'is_own' => $isOwn,
                        'created_at' => $message->created_at,
                        'reactions' => $reactionsGrouped,
                        'reply_to' => $replyInfo,
                        'attachments' => $message->attachments->map(function ($attachment) {
                            return [
                                'id' => $attachment->id,
                                'file_name' => $attachment->file_name,
                                'file_url' => asset('storage/' . $attachment->file_path),
                                'thumbnail_url' => $attachment->thumbnail_path ? asset('storage/' . $attachment->thumbnail_path) : null,
                                'file_type' => $attachment->file_type,
                                'mime_type' => $attachment->mime_type,
                                'file_size' => $attachment->file_size,
                                'formatted_size' => $attachment->getFormattedFileSize(),
                            ];
                        }),
                    ];
                });

            // Mark messages as read
            if ($user) {
                ChatMessage::where('conversation_id', $conversationId)
                    ->where('sender_type', 'company')
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                $conversation->resetUnreadCount('user');
            } elseif ($company) {
                ChatMessage::where('conversation_id', $conversationId)
                    ->where('sender_type', 'user')
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                $conversation->resetUnreadCount('company');
            }

            return response()->json([
                'success' => true,
                'data' => $messages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get new messages since a specific message ID (for polling)
     */
    public function getNewMessages(Request $request, $conversationId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            $conversation = ChatConversation::findOrFail($conversationId);

            // Verify access
            if ($user && $conversation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if ($company && $conversation->company_id !== $company->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $sinceId = $request->get('since', 0);

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->where('id', '>', $sinceId)
                ->where('is_deleted', false)
                ->with(['attachments', 'reactions', 'replyTo.replyMessage'])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) use ($user, $company) {
                    $isOwn = false;
                    if ($user && $message->sender_id === $user->id && $message->sender_type === 'user') {
                        $isOwn = true;
                    } elseif ($company && $message->sender_id === $company->id && $message->sender_type === 'company') {
                        $isOwn = true;
                    }
                    
                    // Group reactions by emoji
                    $reactionsGrouped = $message->reactions->groupBy('emoji')->map(function ($reactions, $emoji) {
                        return [
                            'emoji' => $emoji,
                            'count' => $reactions->count(),
                            'users' => $reactions->map(function ($reaction) {
                                return [
                                    'id' => $reaction->user_id,
                                    'type' => $reaction->user_type,
                                ];
                            })->toArray(),
                        ];
                    })->values();
                    
                    // Get reply info if this message is a reply
                    $replyInfo = null;
                    if ($message->replyTo) {
                        $originalMsg = $message->replyTo->originalMessage;
                        if ($originalMsg) {
                            $replyInfo = [
                                'message_id' => $originalMsg->id,
                                'message_preview' => $originalMsg->message_type === 'text' 
                                    ? (mb_strlen($originalMsg->message) > 50 ? mb_substr($originalMsg->message, 0, 50) . '...' : $originalMsg->message)
                                    : ($originalMsg->message_type === 'image' ? '📷 Image' : '📎 File'),
                            ];
                        }
                    }
                    
                    return [
                        'id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'sender_type' => $message->sender_type,
                        'message' => $message->message,
                        'message_type' => $message->message_type,
                        'is_read' => $message->is_read,
                        'is_own' => $isOwn,
                        'created_at' => $message->created_at,
                        'reactions' => $reactionsGrouped,
                        'reply_to' => $replyInfo,
                        'attachments' => $message->attachments->map(function ($attachment) {
                            return [
                                'id' => $attachment->id,
                                'file_name' => $attachment->file_name,
                                'file_url' => asset('storage/' . $attachment->file_path),
                                'thumbnail_url' => $attachment->thumbnail_path ? asset('storage/' . $attachment->thumbnail_path) : null,
                                'file_type' => $attachment->file_type,
                                'mime_type' => $attachment->mime_type,
                                'file_size' => $attachment->file_size,
                                'formatted_size' => $attachment->getFormattedFileSize(),
                            ];
                        }),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $messages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message (text or with file)
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$request->has('message') && !$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Either message or file is required',
            ], 422);
        }

        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            $conversation = ChatConversation::findOrFail($conversationId);

            // Verify access
            if ($user && $conversation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if ($company && $conversation->company_id !== $company->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            DB::beginTransaction();

            $messageText = $request->message ?? '';
            $messageType = 'text';

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();
                
                $imageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $documentTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
                if (in_array($mimeType, $imageTypes)) {
                    $messageType = 'image';
                } elseif (in_array($mimeType, $documentTypes)) {
                    $messageType = 'file';
                } else {
                    $messageType = 'file';
                }

                if (empty($messageText)) {
                    $messageText = $file->getClientOriginalName();
                }
            }

            // Check if editing existing message
            if ($request->has('message_id')) {
                $existingMessage = ChatMessage::findOrFail($request->message_id);
                
                // Verify ownership
                if (($user && ($existingMessage->sender_id !== $user->id || $existingMessage->sender_type !== 'user')) ||
                    ($company && ($existingMessage->sender_id !== $company->id || $existingMessage->sender_type !== 'company'))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to edit this message',
                    ], 403);
                }
                
                // Update existing message
                $existingMessage->update([
                    'message' => $messageText,
                    'updated_at' => now(),
                ]);
                
                DB::commit();
                
                // Reload message with relationships
                $existingMessage->load(['attachments', 'reactions', 'replyTo.originalMessage']);
                
                return response()->json([
                    'success' => true,
                    'data' => $existingMessage,
                ]);
            }
            
            // Determine sender
            $senderId = $user ? $user->id : $company->id;
            $senderType = $user ? 'user' : 'company';
            
            $message = ChatMessage::create([
                'conversation_id' => $conversationId,
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'message' => $messageText,
                'message_type' => $messageType,
            ]);

            // Handle reply if reply_to message_id is provided
            if ($request->has('reply_to') && $request->reply_to) {
                $replyToMessageId = $request->reply_to;
                // Verify the message exists in the same conversation
                $replyToMessage = ChatMessage::where('id', $replyToMessageId)
                    ->where('conversation_id', $conversationId)
                    ->first();
                
                if ($replyToMessage) {
                    ChatReply::create([
                        'message_id' => $replyToMessageId,
                        'reply_message_id' => $message->id,
                    ]);
                }
            }

            $attachments = [];
            if ($request->hasFile('file')) {
                $attachment = $this->chatService->handleFileUpload($request->file('file'), $message->id);
                $attachments[] = [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_url' => asset('storage/' . $attachment->file_path),
                    'thumbnail_url' => $attachment->thumbnail_path ? asset('storage/' . $attachment->thumbnail_path) : null,
                    'file_type' => $attachment->file_type,
                    'mime_type' => $attachment->mime_type,
                    'file_size' => $attachment->file_size,
                    'formatted_size' => $attachment->getFormattedFileSize(),
                ];
            }

            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
            ]);

            // Increment unread count for the other participant
            if ($user) {
                $conversation->incrementUnreadCount('company');
            } else {
                $conversation->incrementUnreadCount('user');
            }

            DB::commit();

            // Check if recipient is offline and send email notification
            $this->sendOfflineNotification($conversation, $message, $user, $company);

            // Reload message with relationships
            $message->load(['attachments', 'reactions', 'replyTo.originalMessage']);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'sender_type' => $message->sender_type,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'created_at' => $message->created_at,
                    'is_deleted' => $message->is_deleted ?? false,
                    'attachments' => $attachments,
                    'reactions' => $message->reactions ? $message->reactions->map(function ($reaction) {
                        return [
                            'id' => $reaction->id,
                            'emoji' => $reaction->emoji,
                            'user_id' => $reaction->user_id,
                            'user_type' => $reaction->user_type,
                        ];
                    }) : [],
                    'reply_to' => $message->replyTo && $message->replyTo->originalMessage ? [
                        'id' => $message->replyTo->originalMessage->id,
                        'message' => $message->replyTo->originalMessage->message,
                        'sender_id' => $message->replyTo->originalMessage->sender_id,
                        'sender_type' => $message->replyTo->originalMessage->sender_type,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Chat sendMessage error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'conversation_id' => $conversationId ?? null,
                'user_id' => $user->id ?? null,
                'company_id' => $company->id ?? null,
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : 'An error occurred',
            ], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request, $conversationId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            $conversation = ChatConversation::findOrFail($conversationId);

            if (!$user && !$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            // Verify access
            if ($user && $conversation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if ($company && $conversation->company_id !== $company->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Mark messages as read based on who is viewing
            if ($user) {
                ChatMessage::where('conversation_id', $conversationId)
                    ->where('sender_type', 'company')
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                $conversation->resetUnreadCount('user');
            } elseif ($company) {
                ChatMessage::where('conversation_id', $conversationId)
                    ->where('sender_type', 'user')
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                $conversation->resetUnreadCount('company');
            }

            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update activity
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
            
            $presenceService = app(\App\Services\PresenceService::class);
            $userId = $user ? $user->id : $company->id;
            $userType = $user ? 'user' : 'company';
            $status = $presenceService->updateActivity($userId, $userType);

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
     * Add or remove a reaction to a message
     */
    public function toggleReaction(Request $request, $messageId)
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

            $validator = Validator::make($request->all(), [
                'emoji' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $message = ChatMessage::findOrFail($messageId);
            
            // Verify user has access to this conversation
            $conversation = $message->conversation;
            if ($user && $conversation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            if ($company && $conversation->company_id !== $company->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $userId = $user ? $user->id : $company->id;
            $userType = $user ? 'user' : 'company';
            $emoji = $request->emoji;

            // Check if reaction already exists
            $existingReaction = ChatReaction::where('message_id', $messageId)
                ->where('user_id', $userId)
                ->where('user_type', $userType)
                ->where('emoji', $emoji)
                ->first();

            if ($existingReaction) {
                // Remove reaction
                $existingReaction->delete();
                $action = 'removed';
            } else {
                // Add reaction
                ChatReaction::create([
                    'message_id' => $messageId,
                    'user_id' => $userId,
                    'user_type' => $userType,
                    'emoji' => $emoji,
                ]);
                $action = 'added';
            }

            // Get updated reactions
            $reactionsGrouped = $message->fresh()->reactions->groupBy('emoji')->map(function ($reactions, $emoji) {
                return [
                    'emoji' => $emoji,
                    'count' => $reactions->count(),
                    'users' => $reactions->map(function ($reaction) {
                        return [
                            'id' => $reaction->user_id,
                            'type' => $reaction->user_type,
                        ];
                    })->toArray(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'action' => $action,
                'data' => $reactionsGrouped,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start a conversation with a user (for companies)
     */
    public function startConversation(Request $request, $userId)
    {
        try {
            $company = Auth::guard('company')->user();
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company authentication required',
                ], 401);
            }

            // Check if company can chat with this user
            if (!$this->chatService->canChat($company->id, $userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only chat with users who have applied to your jobs or are unlocked',
                ], 403);
            }

            // Get or create conversation
            $conversation = $this->chatService->getOrCreateConversation($company->id, $userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                    'company_id' => $company->id,
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
     * Download chat attachment
     */
    public function downloadAttachment($attachmentId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            
            if (!$user && !$company) {
                abort(401, 'Unauthorized');
            }
            
            $attachment = \App\Models\ChatAttachment::findOrFail($attachmentId);
            $message = $attachment->message;
            $conversation = $message->conversation;
            
            // Verify access - user must be part of the conversation
            if ($user && $conversation->user_id !== $user->id) {
                abort(403, 'Unauthorized access');
            }
            
            if ($company && $conversation->company_id !== $company->id) {
                abort(403, 'Unauthorized access');
            }
            
            // Check if file exists
            $filePath = storage_path('app/public/' . $attachment->file_path);
            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }
            
            return response()->download($filePath, $attachment->file_name, [
                'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error downloading attachment: ' . $e->getMessage());
            abort(404, 'File not found');
        }
    }
    
    /**
     * Update a message
     */
    public function updateMessage(Request $request, $messageId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            $message = ChatMessage::findOrFail($messageId);

            // Verify ownership
            if (($user && ($message->sender_id !== $user->id || $message->sender_type !== 'user')) ||
                ($company && ($message->sender_id !== $company->id || $message->sender_type !== 'company'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this message',
                ], 403);
            }
            
            // Only allow editing text messages
            if ($message->message_type !== 'text') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only text messages can be edited',
                ], 422);
            }

            $message->update([
                'message' => $request->message,
                'updated_at' => now(),
            ]);

            // Reload with relationships
            $message->load(['attachments', 'reactions', 'replyTo.originalMessage']);

            return response()->json([
                'success' => true,
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Delete a message
     */
    public function deleteMessage($messageId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company')->user();
            $message = ChatMessage::findOrFail($messageId);

            // Verify ownership
            if (($user && ($message->sender_id !== $user->id || $message->sender_type !== 'user')) ||
                ($company && ($message->sender_id !== $company->id || $message->sender_type !== 'company'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this message',
                ], 403);
            }

            // Soft delete
            $message->update([
                'is_deleted' => true,
                'deleted_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send email notification if recipient is offline
     */
    private function sendOfflineNotification($conversation, $message, $user, $company)
    {
        try {
            // Determine recipient
            $recipient = null;
            $recipientType = null;
            $sender = null;
            $senderName = null;

            if ($user) {
                // User sent message to company
                $recipient = $conversation->company;
                $recipientType = 'company';
                $sender = $user;
                $senderName = $user->getName() ?: $user->name;
            } else {
                // Company sent message to user
                $recipient = $conversation->user;
                $recipientType = 'user';
                $sender = $company;
                $senderName = $company->name;
            }

            if (!$recipient) {
                return; // No recipient found
            }

            // Check if recipient is offline
            $recipientStatus = ChatUserStatus::where('user_id', $recipient->id)
                ->where('user_type', $recipientType)
                ->first();

            $isOnline = false;
            if ($recipientStatus) {
                $lastActivity = $recipientStatus->last_activity_at;
                if ($lastActivity && $lastActivity->diffInMinutes(now()) <= 5) {
                    $isOnline = $recipientStatus->status === 'online';
                }
            }

            // If recipient is offline, send email notification
            if (!$isOnline && $recipient->email) {
                // Prepare message preview
                $messagePreview = '';
                if ($message->message_type === 'text') {
                    $messagePreview = mb_strlen($message->message) > 100 
                        ? mb_substr($message->message, 0, 100) . '...' 
                        : $message->message;
                }

                // Get recipient name
                $recipientName = $recipientType === 'user' 
                    ? ($recipient->getName() ?: $recipient->name) 
                    : $recipient->name;

                // Build chat URL
                $chatUrl = route('chat.index') . '?conversation=' . $conversation->id;

                // Prepare email data
                $emailData = [
                    'sender_name' => $senderName,
                    'recipient_name' => $recipientName,
                    'recipient_email' => $recipient->email,
                    'message_preview' => $messagePreview,
                    'message_type' => $message->message_type,
                    'chat_url' => $chatUrl,
                ];

                // Send email
                Mail::send(new ChatMessageNotificationMail($emailData));
            }
        } catch (\Exception $e) {
            // Log error but don't fail the message sending
            \Log::error('Error sending offline notification email: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'conversation_id' => $conversation->id ?? null,
                'message_id' => $message->id ?? null,
            ]);
        }
    }
}

