@if(Auth::check() || Auth::guard('company')->check())
    @php $chatUnreadCount = $chatUnreadCount ?? 0; @endphp
    <li class="nav-item chat-header-icon-wrapper">
        <a href="{{ route('chat.index') }}" class="nav-link chat-toggle-btn" id="chatToggleBtn" title="{{ __('Open Full Chat') }}">
            <i class="fas fa-comments"></i>
            <span class="chat-unread-badge" id="chatUnreadBadge" data-initial-count="{{ $chatUnreadCount }}" style="{{ $chatUnreadCount > 0 ? '' : 'display: none;' }}">{{ $chatUnreadCount > 99 ? '99+' : $chatUnreadCount }}</span>
        </a>
        <!-- Chat Dropdown -->
        <div class="chat-header-dropdown" id="chatHeaderDropdown">
            <div class="chat-dropdown-header">
                <h5>{{ __('Recent Conversations') }}</h5>
            </div>
            <div class="chat-dropdown-content" id="chatDropdownContent">
                <div class="chat-dropdown-loading">{{ __('Loading conversations...') }}</div>
            </div>
            <div class="chat-dropdown-footer">
                <a href="{{ route('chat.index') }}" class="chat-dropdown-see-all">{{ __('See all in Messenger') }}</a>
            </div>
        </div>
    </li>
@endif

