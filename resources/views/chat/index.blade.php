@extends('layouts.app')

@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->


<div class="chat-full-page-wrapper">
<div class="chat-full-page">
    <div class="chat-container">
        <!-- Left Sidebar - Chat List -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <a href="{{ Auth::check() ? route('home') : route('company.home') }}" class="chat-back-btn" title="{{ __('Back to Dashboard') }}">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2>{{ __('Chats') }}</h2>
            </div>
            
            <div class="chat-search-container">
                <div class="chat-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="chatSearchInput" placeholder="{{ __('Search Contact') }}" class="chat-search-input">
                </div>
            </div>

            <!-- Chat Filter Tabs -->
            <div class="chat-filter-tabs">
                <button class="chat-tab-btn active" data-filter="all" id="chatTabAll">
                    <span>{{ __('All') }}</span>
                </button>
                <button class="chat-tab-btn" data-filter="unlocked" id="chatTabUnlocked">
                    <span>{{ __('Unlocked') }}</span>
                </button>
                <button class="chat-tab-btn" data-filter="byjobs" id="chatTabByJobs">
                    <span>{{ __('By Jobs') }}</span>
                    <i class="fas fa-chevron-down" id="chatTabByJobsIcon"></i>
                </button>
                <button class="chat-tab-btn" data-filter="unread" id="chatTabUnread">
                    <span>{{ __('Unread') }}</span>
                </button>
            </div>

            <!-- Job Selection Dropdown (for By Jobs tab) -->
            <div class="chat-job-dropdown" id="chatJobDropdown" style="display: none;">
                <div class="chat-job-dropdown-header">
                    <div class="chat-job-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="chatJobSearchInput" placeholder="{{ __('Search jobs...') }}" class="chat-job-search-input">
                    </div>
                    <button class="chat-job-dropdown-close" id="chatJobDropdownClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="chat-job-dropdown-list" id="chatJobDropdownList">
                    <div class="chat-loading">
                        <i class="fas fa-spinner fa-spin"></i> {{ __('Loading jobs...') }}
                    </div>
                </div>
            </div>

            <!-- Selected Job Title (shown when a job is selected) -->
            <div class="chat-selected-job-title" id="chatSelectedJobTitle" style="display: none;">
                <i class="fas fa-briefcase"></i>
                <span></span>
            </div>

            <div class="chat-list" id="chatConversationsList">
                <div class="chat-loading">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('Loading conversations...') }}
                </div>
            </div>

        </div>

        <!-- Right Main Area - Active Chat -->
        <div class="chat-main">
            <div class="chat-empty-state" id="chatEmptyState">
                <div class="empty-state-content">
                    <i class="fas fa-comments"></i>
                    <h3>{{ __('Select a conversation') }}</h3>
                    <p>{{ __('Choose a conversation from the list to start chatting') }}</p>
                </div>
            </div>

            <!-- Active Chat View (hidden by default) -->
            <div class="chat-active" id="chatActiveView" style="display: none;">
                <!-- Chat Header -->
                <div class="chat-header" id="chatHeader">
                    <div class="chat-header-info">
                        <!-- Mobile Back Button -->
                        <button class="chat-mobile-back-btn" id="chatMobileBackBtn" title="{{ __('Back to conversations') }}">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="chat-avatar-wrapper">
                            <img id="chatHeaderAvatar" src="" alt="" class="chat-avatar">
                            <span class="chat-status-indicator chat-status-online" id="chatHeaderStatus"></span>
                        </div>
                        <div class="chat-header-details">
                            <h3 id="chatHeaderName">{{ __('Contact Name') }}</h3>
                            <p id="chatHeaderSubtitle">{{ __('UI / UX Designer') }}</p>
                        </div>
                    </div>
                    <div class="chat-header-actions">
                        <button class="chat-header-action-btn" id="chatHeaderSearchBtn" title="{{ __('Search') }}">
                            <i class="fas fa-search"></i>
                        </button>
                       
                    </div>
                </div>
                
                <!-- Search Overlay -->
                <div class="chat-search-overlay" id="chatSearchOverlay" style="display: none;">
                    <div class="chat-search-header">
                        <div class="chat-search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="chatMessageSearchInput" placeholder="{{ __('Search messages...') }}" class="chat-message-search-input" autocomplete="off">
                            <button class="chat-search-close-btn" id="chatSearchCloseBtn" title="{{ __('Close') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chat-search-results" id="chatSearchResults">
                        <div class="chat-search-empty">{{ __('Type to search messages...') }}</div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="chat-messages-container">
                    <div class="chat-messages" id="chatMessages">
                        <div class="chat-loading-messages">
                            <i class="fas fa-spinner fa-spin"></i> {{ __('Loading messages...') }}
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div class="chat-typing-indicator" id="chatTypingIndicator" style="display: none;">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="typing-text" id="typingText">{{ __('is typing...') }}</span>
                </div>

                <!-- Message Input Area -->
                <div class="chat-input-container">
                    <div class="chat-input-wrapper">
                        <textarea id="chatMessageInput" placeholder="{{ __('Type a message here..') }}" class="chat-message-input" rows="1"></textarea>
                        <div class="chat-input-actions">
                            <button class="chat-input-icon-btn" id="chatEmojiBtn" title="{{ __('Emoji') }}">
                                <i class="fas fa-smile"></i>
                            </button>
                            <button class="chat-input-icon-btn" id="chatFileBtn" title="{{ __('Attach File') }}">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button class="chat-send-btn" id="chatSendBtn" title="{{ __('Send') }}">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <input type="file" id="chatFileInput" multiple style="display: none;" accept=".jpg,.jpeg,.png,.gif,.doc,.docx,.pdf,image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                </div>
                
                <!-- Image Preview Container -->
                <div class="chat-image-preview-container" id="chatImagePreviewContainer" style="display: none;">
                    <div class="chat-image-preview-list" id="chatImagePreviewList"></div>
                </div>
                
                <!-- Emoji Picker -->
                <div class="chat-emoji-picker" id="chatEmojiPicker" style="display: none;">
                    <div class="emoji-picker-header">
                        <span>{{ __('Select Emoji') }}</span>
                        <button class="emoji-picker-close" id="emojiPickerClose">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="emoji-picker-content" id="emojiPickerContent">
                        <!-- Emojis will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Footer start -->
@include('includes.footer')
<!-- Footer end -->

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat-full-page.css') }}">
@endpush

@push('scripts')
<script>
    window.CHAT_BASE_URL = '{{ url("/") }}';
    window.CHAT_MODE = 'fullpage';
    window.CHAT_TRANSLATIONS = {
        loadingMessages: '{{ __("Loading messages...") }}',
        isTyping: '{{ __("is typing...") }}',
        typeMessage: '{{ __("Type a message here..") }}',
        attachFile: '{{ __("Attach File") }}',
        send: '{{ __("Send") }}',
        emoji: '{{ __("Emoji") }}',
        selectEmoji: '{{ __("Select Emoji") }}',
        searchMessages: '{{ __("Search messages...") }}',
        typeToSearch: '{{ __("Type to search messages...") }}',
        close: '{{ __("Close") }}',
        search: '{{ __("Search") }}',
        backToConversations: '{{ __("Back to conversations") }}',
        contactName: '{{ __("Contact Name") }}',
        uiUxDesigner: '{{ __("UI / UX Designer") }}',
        justNow: '{{ __("Just now") }}'
    };
    @if(Auth::guard('company')->check())
    window.currentCompany = { id: {{ Auth::guard('company')->user()->id }}, type: 'company' };
    window.currentUser = null;
    @elseif(Auth::check())
    window.currentUser = { id: {{ Auth::user()->id }}, type: 'user' };
    window.currentCompany = null;
    @endif
</script>
<script src="{{ asset('js/chat-full-page.js') }}"></script>
@endpush
@endsection

