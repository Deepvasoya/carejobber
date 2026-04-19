/**
 * Full-Page Chat Interface - Professional Design
 * Based on chat-widget-polling.js but adapted for full-page view
 */

(function() {
"use strict";

class FullPageChat {
    constructor() {
        this.activeConversationId = null;
        this.activeConversation = null;
        this.conversations = [];
        this.allConversations = []; // Store all conversations for filtering
        this.messages = new Map();
        this.pollingInterval = null;
        this.reactionPollingInterval = null; // For polling reaction changes
        this.lastMessageIds = new Map();
        this.pendingFiles = [];
        this.baseUrl = window.CHAT_BASE_URL || '';
        this.currentFilter = 'all';
        this.selectedJobId = null;
        this.replyingToMessageId = null; // Track which message is being replied to
        this.editingMessageId = null; // Track which message is being edited
        this.typingTimeout = null; // For typing indicator timeout
        this.typingPollingInterval = null; // For polling typing status
        this.audioContext = null; // For beep sound generation
        this.init();
    }

    init() {
        // Check if user is authenticated before initializing
        const user = window.currentUser || { id: null, type: null };
        const company = window.currentCompany || { id: null };
        
        if (!user.id && !company.id) {
            return; // Don't initialize if user is not authenticated
        }

        // Initialize mobile view state
        if (this.isMobile()) {
            this.showMobileConversationList();
        }

        this.setupEventListeners();
        this.initializeAudio(); // Initialize audio on user interaction
        
        // Check for conversation parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const conversationId = urlParams.get('conversation');
        
        // Load conversations first, then open the conversation if specified
        this.loadConversations().then(() => {
            if (conversationId) {
                // Wait a bit for DOM to be ready, then open conversation
                setTimeout(() => {
                    const convId = parseInt(conversationId);
                    // Check if conversation exists in loaded list
                    const conversation = this.conversations.find(c => c.id === convId);
                    if (conversation) {
                        this.openConversation(convId);
                    } else {
                        // If not found, try loading all conversations (no filter)
                        this.loadConversations('all').then(() => {
                            const conv = this.conversations.find(c => c.id === convId);
                            if (conv) {
                                this.openConversation(convId);
                            } else {
                                console.warn('Conversation not found:', convId);
                            }
                        });
                    }
                    // Clean up URL (remove parameter)
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }, 300);
            }
        });
        
        this.startPolling();
        this.updateActivity();
    }

    setupEventListeners() {
        // Search
        const searchInput = document.getElementById('chatSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.filterConversations(e.target.value));
        }

        // Message input (textarea)
        const messageInput = document.getElementById('chatMessageInput');
        if (messageInput) {
            // Auto-resize textarea
            messageInput.addEventListener('input', () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
                // Send typing indicator when user types
                if (this.activeConversationId) {
                    this.handleTyping(this.activeConversationId);
                }
            });
            
            // Send on Enter (Shift+Enter for new line)
            messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                    // Stop typing indicator when message is sent
                    if (this.activeConversationId) {
                        this.sendTypingIndicator(this.activeConversationId, false);
                    }
                }
            });
        }

        // Send button
        const sendBtn = document.getElementById('chatSendBtn');
        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }

        // File attachment button
        const fileBtn = document.getElementById('chatFileBtn');
        const fileInput = document.getElementById('chatFileInput');
        if (fileBtn && fileInput) {
            fileBtn.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        // Emoji button
        const emojiBtn = document.getElementById('chatEmojiBtn');
        const emojiPicker = document.getElementById('chatEmojiPicker');
        const emojiPickerClose = document.getElementById('emojiPickerClose');
        
        if (emojiBtn && emojiPicker) {
            emojiBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isVisible = emojiPicker.style.display === 'block';
                emojiPicker.style.display = isVisible ? 'none' : 'block';
                if (!isVisible) {
                    // Always re-initialize to ensure emojis are rendered
                    const emojiPickerContent = document.getElementById('emojiPickerContent');
                    if (emojiPickerContent) {
                        emojiPickerContent.dataset.initialized = 'false';
                    }
                    this.initEmojiPicker();
                }
            });
        }

        if (emojiPickerClose && emojiPicker) {
            emojiPickerClose.addEventListener('click', (e) => {
                e.stopPropagation();
                emojiPicker.style.display = 'none';
            });
        }

        // Close emoji picker when clicking outside
        document.addEventListener('click', (e) => {
            if (emojiPicker && emojiBtn && !emojiPicker.contains(e.target) && !emojiBtn.contains(e.target)) {
                emojiPicker.style.display = 'none';
            }
        });
        
        // Initialize filter tabs
        this.initFilterTabs();
        
        // Chat message search
        this.initMessageSearch();
        
        // Mobile back button
        const mobileBackBtn = document.getElementById('chatMobileBackBtn');
        if (mobileBackBtn) {
            mobileBackBtn.addEventListener('click', () => {
                this.showMobileConversationList();
            });
        }
        
        // Handle window resize (for orientation changes)
        window.addEventListener('resize', () => {
            // If switching from mobile to desktop, reset view states
            if (!this.isMobile()) {
                const sidebar = document.querySelector('.chat-sidebar');
                const mainChat = document.querySelector('.chat-main');
                if (sidebar) {
                    sidebar.classList.remove('chat-mobile-hidden');
                }
                if (mainChat) {
                    mainChat.classList.remove('chat-mobile-active');
                }
            } else if (this.isMobile() && !this.activeConversationId) {
                // If switching to mobile and no conversation is active, show list
                this.showMobileConversationList();
            }
        });
        
        // Use event delegation for message actions (reactions, reply, etc.)
        document.addEventListener('click', (e) => {
            // Reaction picker item click (inline in footer)
            if (e.target.closest('.chat-reaction-picker-item')) {
                const btn = e.target.closest('.chat-reaction-picker-item');
                const picker = btn.closest('.chat-reaction-picker');
                const messageId = picker ? picker.dataset.messageId : null;
                const emoji = btn.dataset.emoji;
                if (messageId && emoji) {
                    this.toggleReaction(messageId, emoji);
                }
            }
            
            // Reaction item click (existing reactions)
            if (e.target.closest('.chat-reaction-item')) {
                const btn = e.target.closest('.chat-reaction-item');
                const messageId = btn.dataset.messageId;
                const emoji = btn.dataset.emoji;
                this.toggleReaction(messageId, emoji);
            }
            
            // Reply button
            if (e.target.closest('.chat-reply-btn')) {
                const btn = e.target.closest('.chat-reply-btn');
                const messageId = btn.dataset.messageId;
                this.startReply(messageId);
            }
            
            // Cancel reply
            if (e.target.closest('.chat-reply-cancel')) {
                this.cancelReply();
            }
            
            // Edit message
            if (e.target.closest('.chat-edit-btn')) {
                const btn = e.target.closest('.chat-edit-btn');
                const messageId = btn.dataset.messageId;
                if (messageId) {
                    this.startEdit(parseInt(messageId));
                }
            }
            
            // Cancel edit
            if (e.target.closest('.chat-edit-cancel')) {
                this.cancelEdit();
            }
            
            // Delete message
            if (e.target.closest('.chat-delete-btn')) {
                const btn = e.target.closest('.chat-delete-btn');
                const messageId = btn.dataset.messageId;
                if (messageId) {
                    this.deleteMessage(parseInt(messageId));
                }
            }
        });
    }
    
    initFilterTabs() {
        const tabButtons = document.querySelectorAll('.chat-tab-btn');
        const jobDropdown = document.getElementById('chatJobDropdown');
        const jobDropdownClose = document.getElementById('chatJobDropdownClose');
        const jobSearchInput = document.getElementById('chatJobSearchInput');
        const byJobsTab = document.getElementById('chatTabByJobs');
        
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                
                // Remove active class from all tabs
                tabButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                this.currentFilter = filter;
                
                if (filter === 'byjobs') {
                    // Toggle job dropdown
                    if (jobDropdown.style.display === 'none' || !jobDropdown.style.display) {
                        jobDropdown.style.display = 'flex';
                        this.loadJobs();
                        // If no job is selected, clear the job title
                        if (!this.selectedJobId) {
                            this.updateSelectedJobTitle(null);
                        }
                    } else {
                        jobDropdown.style.display = 'none';
                    }
                } else {
                    // Hide job dropdown for other filters
                    if (jobDropdown) jobDropdown.style.display = 'none';
                    this.selectedJobId = null;
                    // Clear selected job title when switching to other filters
                    this.updateSelectedJobTitle(null);
                    this.loadConversations(filter);
                }
            });
        });
        
        // Close job dropdown
        if (jobDropdownClose) {
            jobDropdownClose.addEventListener('click', () => {
                if (jobDropdown) jobDropdown.style.display = 'none';
            });
        }
        
        // Job search
        if (jobSearchInput) {
            let searchTimeout;
            jobSearchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const search = e.target.value;
                searchTimeout = setTimeout(() => {
                    this.loadJobs(search);
                }, 300);
            });
        }
    }
    
    async loadJobs(search = '') {
        try {
            let url = this.baseUrl + '/chat/jobs';
            if (search) {
                url += '?search=' + encodeURIComponent(search);
            }
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load jobs');

            const data = await response.json();
            this.renderJobs(data.data || []);
        } catch (error) {
            console.error('Error loading jobs:', error);
        }
    }
    
    renderJobs(jobs) {
        const container = document.getElementById('chatJobDropdownList');
        if (!container) return;
        
        if (jobs.length === 0) {
            container.innerHTML = '<div class="chat-loading">No jobs found</div>';
            return;
        }
        
        // Determine if user or company
        const user = window.currentUser || { id: null, type: null };
        const company = window.currentCompany || { id: null };
        const isUser = user.id && !company.id;
        const metaText = isUser ? 'Applied' : 'Posted';
        
        container.innerHTML = jobs.map(job => `
            <div class="chat-job-item ${this.selectedJobId === job.id ? 'active' : ''}" 
                 data-job-id="${job.id}"
                 onclick="fullPageChat.selectJob(${job.id}, '${this.escapeHtml(job.title)}')">
                <div class="chat-job-item-content">
                    <div class="chat-job-item-info">
                        <div class="chat-job-item-title">${this.escapeHtml(job.title)}</div>
                        <div class="chat-job-item-meta">${metaText} ${job.created_at}</div>
                    </div>
                    <div class="chat-job-item-count">
                        <span class="chat-job-count-badge">${job.chat_count || 0}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    selectJob(jobId, jobTitle) {
        this.selectedJobId = jobId;
        this.currentFilter = 'byjobs';
        
        // Update active state
        document.querySelectorAll('.chat-job-item').forEach(item => {
            item.classList.remove('active');
        });
        const selectedItem = document.querySelector(`[data-job-id="${jobId}"]`);
        if (selectedItem) selectedItem.classList.add('active');
        
        // Keep button text as "By Jobs" - show job title above conversation list instead
        this.updateSelectedJobTitle(jobTitle);
        
        // Hide dropdown and load conversations
        const jobDropdown = document.getElementById('chatJobDropdown');
        if (jobDropdown) jobDropdown.style.display = 'none';
        
        this.loadConversations('byjobs', jobId);
    }
    
    updateSelectedJobTitle(jobTitle) {
        // Create or update the selected job title display above conversation list
        let jobTitleElement = document.getElementById('chatSelectedJobTitle');
        if (!jobTitleElement) {
            const container = document.getElementById('chatConversationsList');
            if (container && container.parentElement) {
                jobTitleElement = document.createElement('div');
                jobTitleElement.id = 'chatSelectedJobTitle';
                jobTitleElement.className = 'chat-selected-job-title';
                container.parentElement.insertBefore(jobTitleElement, container);
            }
        }
        
        if (jobTitleElement && jobTitle) {
            jobTitleElement.innerHTML = `
                <i class="fas fa-briefcase"></i>
                <span>${this.escapeHtml(jobTitle)}</span>
            `;
            jobTitleElement.style.display = 'flex';
        } else if (jobTitleElement && !jobTitle) {
            jobTitleElement.style.display = 'none';
        }
    }

    async loadConversations(filter = 'all', jobId = null) {
        // Check if user is authenticated
        const user = window.currentUser || { id: null, type: null };
        const company = window.currentCompany || { id: null };
        
        if (!user.id && !company.id) {
            return; // Don't load if user is not authenticated
        }

        try {
            let url = this.baseUrl + '/chat/conversations?filter=' + filter;
            if (jobId) {
                url += '&job_id=' + jobId;
            }
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                // If 401 or 403, user is not authenticated, silently fail
                if (response.status === 401 || response.status === 403) {
                    return;
                }
                throw new Error('Failed to load conversations');
            }

            const data = await response.json();
            this.conversations = data.data || [];
            if (filter === 'all') {
                this.allConversations = this.conversations; // Store all for quick switching
            }
            this.renderConversations();
        } catch (error) {
            console.error('Error loading conversations:', error);
            // Don't show error on login/register pages
            if (!window.location.pathname.includes('login') && !window.location.pathname.includes('register')) {
                this.showError('Failed to load conversations');
            }
        }
    }

    renderConversations() {
        const container = document.getElementById('chatConversationsList');
        if (!container) return;

        if (this.conversations.length === 0) {
            container.innerHTML = '<div class="chat-loading">No conversations yet</div>';
            return;
        }

        container.innerHTML = this.conversations.map(conv => {
            const participant = conv.other_participant || conv.company || conv.user;
            if (!participant) return '';

            const lastMsg = conv.last_message;
            const preview = lastMsg ? (lastMsg.message_type === 'image' ? '📷 Image' : 
                                      lastMsg.message_type === 'file' ? '📎 File' : 
                                      lastMsg.message) : 'No messages yet';
            
            const isActive = this.activeConversationId === conv.id;
            const unreadBadge = conv.unread_count > 0 ? 
                `<span class="chat-list-item-badge">${conv.unread_count}</span>` : '';
            
            const date = lastMsg ? this.formatDate(lastMsg.created_at) : '';
            const readReceipt = `<i class="fas fa-check chat-list-item-read-receipt"></i>`;
            
            // Get status from participant data (default to offline if not set)
            const participantStatus = participant.status || 'offline';
            const statusClass = `chat-status-${participantStatus}`;

            return `
                <div class="chat-list-item ${isActive ? 'active' : ''} ${conv.unread_count > 0 ? 'chat-list-item-unread' : ''}" 
                     data-conversation-id="${conv.id}"
                     onclick="fullPageChat.openConversation(${conv.id})">
                    <div class="chat-list-item-avatar">
                        <img src="${participant.logo || '/images/default-user.png'}" alt="${this.escapeHtml(participant.name)}">
                        <span class="chat-status-indicator ${statusClass}"></span>
                    </div>
                    <div class="chat-list-item-content">
                        <div class="chat-list-item-header">
                            <h4 class="chat-list-item-name">${this.escapeHtml(participant.name)}</h4>
                            <span class="chat-list-item-date">
                                ${date}
                                ${readReceipt}
                            </span>
                        </div>
                        <p class="chat-list-item-message">${this.escapeHtml(preview)}</p>
                    </div>
                    ${unreadBadge}
                </div>
            `;
        }).join('');
    }

    async openConversation(conversationId) {
        this.activeConversationId = conversationId;
        const conversation = this.conversations.find(c => c.id === conversationId);
        
        if (!conversation) {
            console.error('Conversation not found');
            return;
        }

        this.activeConversation = conversation;
        const participant = conversation.other_participant || conversation.company || conversation.user;

        // Update header
        const headerAvatar = document.getElementById('chatHeaderAvatar');
        const headerName = document.getElementById('chatHeaderName');
        const headerSubtitle = document.getElementById('chatHeaderSubtitle');
        const headerStatus = document.getElementById('chatHeaderStatus');
        
        if (headerAvatar) headerAvatar.src = participant.logo || '/images/default-user.png';
        if (headerName) {
            headerName.innerHTML = this.getProfileLink(participant);
        }
        if (headerSubtitle) {
            // Show status and last seen information
            const participantStatus = participant.status || 'offline';
            const lastSeenAt = participant.last_seen_at || participant.last_activity_at;
            headerSubtitle.textContent = this.formatUserStatus(participantStatus, lastSeenAt);
        }
        
        // Update status indicator in header
        if (headerStatus) {
            const participantStatus = participant.status || 'offline';
            headerStatus.className = `chat-status-indicator chat-status-${participantStatus}`;
        }

        // Show active view, hide empty state
        document.getElementById('chatEmptyState').style.display = 'none';
        document.getElementById('chatActiveView').style.display = 'flex';
        
        // Mobile: Show chat view, hide sidebar
        this.showMobileChatView();

        // Update active state in list
        document.querySelectorAll('.chat-list-item').forEach(item => {
            item.classList.remove('active');
        });
        const activeItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (activeItem) activeItem.classList.add('active');

        // Clear any active reply when switching conversations
        this.replyingToMessageId = null;
        this.updateReplyPreview();
        
        // Hide typing indicator when switching conversations
        this.hideTypingIndicator();
        
        // Stop previous typing polling
        if (this.typingPollingInterval) {
            clearInterval(this.typingPollingInterval);
            this.typingPollingInterval = null;
        }
        
        // Load messages
        await this.loadMessages(conversationId);
        
        // Mark as read
        await this.markAsRead(conversationId);
        
        // Start polling for this conversation
        this.startConversationPolling(conversationId);
        
        // Start typing status polling
        this.startTypingPolling(conversationId);
        
        // Start typing status polling
        this.startTypingPolling(conversationId);
    }

    async loadMessages(conversationId, searchQuery = '') {
        try {
            let url = `${this.baseUrl}/chat/conversations/${conversationId}/messages`;
            if (searchQuery) {
                url += `?search=${encodeURIComponent(searchQuery)}`;
            }
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load messages');

            const data = await response.json();
            const messages = data.data || [];
            
            // Store old reactions before updating to detect changes
            const oldMessages = this.messages.get(conversationId) || [];
            const oldReactionsMap = new Map();
            oldMessages.forEach(msg => {
                if (msg.reactions && msg.reactions.length > 0) {
                    oldReactionsMap.set(msg.id, msg.reactions);
                }
            });
            
            this.messages.set(conversationId, messages);
            
            if (messages.length > 0) {
                this.lastMessageIds.set(conversationId, messages[messages.length - 1].id);
                console.log('Loaded messages for conversation', conversationId, '- Last message ID:', messages[messages.length - 1].id);
            } else {
                // Initialize with 0 if no messages yet
                this.lastMessageIds.set(conversationId, 0);
                console.log('No messages yet for conversation', conversationId, '- Initialized lastMessageId to 0');
            }
            
            if (!searchQuery) {
                // Check for new reactions from others after loading messages
                // This catches reactions added by others while user is viewing the conversation
                messages.forEach(msg => {
                    if (msg.reactions && msg.reactions.length > 0) {
                        const oldReactions = oldReactionsMap.get(msg.id) || [];
                        // Only check if we had previous messages (not initial load)
                        if (oldMessages.length > 0) {
                            this.checkForReactionChanges(msg.id, oldReactions);
                        }
                    }
                });
                
                // Only render messages if not searching (search results are shown separately)
                this.renderMessages(conversationId, messages);
            }
            
            return messages;
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Failed to load messages');
            return [];
        }
    }

    renderMessages(conversationId, messages) {
        const container = document.getElementById('chatMessages');
        if (!container) return;

        const user = window.currentUser || { id: null, type: null };
        const company = window.currentCompany || { id: null };

        // Remove duplicates by message ID before rendering
        const uniqueMessages = messages.filter((msg, index, self) => 
            index === self.findIndex(m => m.id === msg.id)
        );

        container.innerHTML = uniqueMessages.map(msg => {
            const isOwn = (user.id && msg.sender_id === user.id && msg.sender_type === 'user') ||
                         (company.id && msg.sender_id === company.id && msg.sender_type === 'company');
            
            // Show deleted message indicator
            if (msg.is_deleted) {
                return `
                    <div class="chat-message ${isOwn ? 'own' : ''}" data-message-id="${msg.id}">
                        <div class="chat-message-content">
                            <div class="chat-message-bubble chat-message-deleted">
                                <i class="fas fa-trash"></i> This message was deleted
                            </div>
                        </div>
                    </div>
                `;
            }
            
            const time = this.formatTime(msg.created_at);
            let content = '';

            if (msg.message_type === 'image' && msg.attachments && msg.attachments.length > 0) {
                const attachment = msg.attachments[0];
                content = `
                    <div class="chat-message-bubble">
                        <img src="${attachment.file_url}" alt="Image" style="max-width: 200px; border-radius: 8px;">
                    </div>
                `;
            } else if (msg.message_type === 'file' && msg.attachments && msg.attachments.length > 0) {
                const attachment = msg.attachments[0];
                const fileIcon = this.getFileIcon(attachment.mime_type, attachment.file_name);
                const fileSize = attachment.formatted_size || this.formatFileSize(attachment.file_size || 0);
                const downloadUrl = `${this.baseUrl}/chat/attachments/${attachment.id}/download`;
                
                content = `
                    <div class="chat-message-bubble chat-file-attachment">
                        <div class="chat-file-attachment-content">
                            <div class="chat-file-attachment-icon">
                                <i class="${fileIcon}"></i>
                            </div>
                            <div class="chat-file-attachment-info">
                                <div class="chat-file-attachment-name">${this.escapeHtml(attachment.file_name)}</div>
                                <div class="chat-file-attachment-size">${fileSize}</div>
                            </div>
                            <a href="${downloadUrl}" class="chat-file-download-btn" download title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                // Check if message contains URL for link preview
                const urlMatch = msg.message.match(/(https?:\/\/[^\s]+)/);
                if (urlMatch) {
                    const url = urlMatch[1];
                    const messageText = msg.message.replace(url, '').trim();
                    content = `
                        <div class="chat-message-bubble">
                            ${messageText ? this.escapeHtml(messageText) : ''}
                            <div class="chat-message-link-preview">
                                <div class="chat-message-link-preview-images">
                                    <img src="/images/default-link-preview.jpg" alt="Preview" onerror="this.style.display='none'">
                                    <img src="/images/default-link-preview.jpg" alt="Preview" onerror="this.style.display='none'">
                                </div>
                                <div class="chat-message-link-preview-content">
                                    <p class="chat-message-link-preview-title">Website Title</p>
                                    <a href="${url}" target="_blank" class="chat-message-link-preview-url">${url}</a>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Check if message is emoji-only or contains emojis
                    const messageText = msg.message || '';
                    const emojiOnlyRegex = /^[\p{Emoji}\s]+$/u;
                    const hasEmojis = /[\p{Emoji}]/u.test(messageText);
                    const isEmojiOnly = emojiOnlyRegex.test(messageText.trim());
                    
                    // Add class for emoji-only messages or messages with emojis
                    const bubbleClass = isEmojiOnly ? 'chat-message-bubble emoji-only' : 
                                       hasEmojis ? 'chat-message-bubble has-emojis' : 
                                       'chat-message-bubble';
                    
                    // Process message: wrap emojis in spans, escape other content
                    let displayMessage = '';
                    const emojiRegex = /[\p{Emoji}]/gu;
                    let lastIndex = 0;
                    let match;
                    
                    while ((match = emojiRegex.exec(messageText)) !== null) {
                        // Escape text before emoji
                        if (match.index > lastIndex) {
                            displayMessage += this.escapeHtml(messageText.substring(lastIndex, match.index));
                        }
                        // Wrap emoji in span (don't escape emoji)
                        displayMessage += `<span class="emoji-char">${match[0]}</span>`;
                        lastIndex = match.index + match[0].length;
                    }
                    
                    // Escape remaining text after last emoji
                    if (lastIndex < messageText.length) {
                        displayMessage += this.escapeHtml(messageText.substring(lastIndex));
                    }
                    
                    // If no emojis found, just escape the whole message
                    if (!hasEmojis) {
                        displayMessage = this.escapeHtml(messageText);
                    }
                    
                    content = `<div class="${bubbleClass}">${displayMessage}</div>`;
                }
            }

            const activeConv = this.activeConversation;
            const participant = activeConv ? (activeConv.other_participant || activeConv.company || activeConv.user) : null;
            const avatarSrc = participant ? (participant.logo || '/images/default-user.png') : '/images/default-user.png';

            // Only show avatar for incoming messages (not own)
            const avatarHtml = !isOwn ? `<img src="${avatarSrc}" alt="" class="chat-message-avatar">` : '';

            // Render reply preview if this message is a reply
            let replyPreview = '';
            if (msg.reply_to) {
                replyPreview = `
                    <div class="chat-message-reply-preview" data-reply-to="${msg.reply_to.message_id}">
                        <div class="chat-message-reply-preview-content">
                            <i class="fas fa-reply"></i>
                            <span>${this.escapeHtml(msg.reply_to.message_preview)}</span>
                        </div>
                    </div>
                `;
            }

            // Render reactions
            let reactionsHtml = '';
            if (msg.reactions && msg.reactions.length > 0) {
                const reactionsList = msg.reactions.map(reaction => {
                    const user = window.currentUser || { id: null, type: null };
                    const company = window.currentCompany || { id: null };
                    const hasReacted = reaction.users.some(u => 
                        (user.id && u.id === user.id && u.type === 'user') ||
                        (company.id && u.id === company.id && u.type === 'company')
                    );
                    return `
                        <button class="chat-reaction-item ${hasReacted ? 'active' : ''}" 
                                data-message-id="${msg.id}" 
                                data-emoji="${reaction.emoji}"
                                title="Click to ${hasReacted ? 'remove' : 'add'} reaction">
                            <span class="chat-reaction-emoji">${reaction.emoji}</span>
                            <span class="chat-reaction-count">${reaction.count}</span>
                        </button>
                    `;
                }).join('');
                
                reactionsHtml = `
                    <div class="chat-message-reactions">
                        ${reactionsList}
                    </div>
                `;
            }

            // Reaction picker (inline in footer)
            const reactionPicker = `
                <div class="chat-reaction-picker" data-message-id="${msg.id}">
                    <button class="chat-reaction-picker-item" data-emoji="👍" title="Like">👍</button>
                    <button class="chat-reaction-picker-item" data-emoji="❤️" title="Love">❤️</button>
                    <button class="chat-reaction-picker-item" data-emoji="😂" title="Haha">😂</button>
                    <button class="chat-reaction-picker-item" data-emoji="😮" title="Wow">😮</button>
                    <button class="chat-reaction-picker-item" data-emoji="😢" title="Sad">😢</button>
                    <button class="chat-reaction-picker-item" data-emoji="🙏" title="Thanks">🙏</button>
                </div>
            `;

            // Action buttons (reply, edit, delete) - only show edit/delete for own messages
            let actionButtons = `
                <div class="chat-message-actions">
                    <button class="chat-message-action-btn chat-reply-btn" 
                            data-message-id="${msg.id}" 
                            title="Reply">
                        <i class="fas fa-reply"></i>
                    </button>
            `;
            
            // Only show edit/delete for own messages and text messages (not files/images)
            if (isOwn && msg.message_type === 'text' && !msg.is_deleted) {
                actionButtons += `
                    <button class="chat-message-action-btn chat-edit-btn" 
                            data-message-id="${msg.id}" 
                            title="Edit">
                        <i class="fas fa-pencil"></i>
                    </button>
                    <button class="chat-message-action-btn chat-delete-btn" 
                            data-message-id="${msg.id}" 
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            }
            
            actionButtons += `</div>`;

            return `
                <div class="chat-message ${isOwn ? 'own' : ''}" data-message-id="${msg.id}">
                    ${avatarHtml}
                    <div class="chat-message-content">
                        ${replyPreview}
                        ${content}
                        ${reactionsHtml}
                        <span class="chat-message-time">${time}</span>
                        <div class="chat-message-footer">
                            ${reactionPicker}
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        this.scrollToBottom();
        this.updateReplyPreview(); // Update reply preview after rendering messages
        
        // Add click handlers for reply previews to scroll to original message
        this.attachReplyPreviewClickHandlers();
    }
    
    attachReplyPreviewClickHandlers() {
        const replyPreviews = document.querySelectorAll('.chat-message-reply-preview[data-reply-to]');
        replyPreviews.forEach(preview => {
            // Remove existing listeners to avoid duplicates
            const newPreview = preview.cloneNode(true);
            preview.parentNode.replaceChild(newPreview, preview);
            
            newPreview.addEventListener('click', (e) => {
                e.stopPropagation();
                const originalMessageId = parseInt(newPreview.dataset.replyTo);
                if (originalMessageId) {
                    this.scrollToMessage(originalMessageId);
                }
            });
        });
    }

    async sendMessage() {
        const input = document.getElementById('chatMessageInput');
        // Use .value directly, don't trim immediately (emojis might be at the end)
        const message = input ? input.value : '';
        const trimmedMessage = message.trim();

        if (!trimmedMessage && this.pendingFiles.length === 0) return;
        
        // Reset textarea height after sending
        if (input) {
            input.style.height = 'auto';
        }
        if (!this.activeConversationId) return;

        try {
            // If editing, use PUT with JSON body
            if (this.editingMessageId) {
                console.log('Editing message', this.editingMessageId, 'with text:', trimmedMessage);
                const response = await fetch(`${this.baseUrl}/chat/messages/${this.editingMessageId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message: trimmedMessage })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: 'Failed to update message' }));
                    throw new Error(errorData.message || 'Failed to update message');
                }

                const result = await response.json();
                console.log('Edit response:', result);
                
                if (result.success) {
                    // Reload messages to show updated message
                    await this.loadMessages(this.activeConversationId);
                    this.editingMessageId = null;
                    this.updateEditPreview();
                    
                    const input = document.getElementById('chatMessageInput');
                    if (input) {
                        input.value = '';
                        input.style.height = 'auto';
                    }
                } else {
                    throw new Error(result.message || 'Failed to update message');
                }
                return; // Exit early for edit
            }
            
            // For new messages, use FormData
            const formData = new FormData();
            if (trimmedMessage) {
                formData.append('message', trimmedMessage);
            }

            // Add reply_to if replying to a message
            if (this.replyingToMessageId) {
                formData.append('reply_to', this.replyingToMessageId);
            }

            this.pendingFiles.forEach(file => {
                formData.append('file', file);
            });
            
            // Clear input immediately before API call
            if (input) {
                input.value = '';
                input.style.height = 'auto';
            }
            const filesToClear = [...this.pendingFiles];
            this.pendingFiles = [];
            this.showImagePreviews();
            const replyIdToSend = this.replyingToMessageId;
            this.replyingToMessageId = null;
            this.updateReplyPreview();
            
            const response = await fetch(`${this.baseUrl}/chat/conversations/${this.activeConversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: formData
            });

            if (!response.ok) {
                let errorMessage = 'Failed to send message';
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorData.error || errorMessage;
                    console.error('Send message error response:', errorData);
                } catch (e) {
                    errorMessage = `Server error: ${response.status} ${response.statusText}`;
                }
                throw new Error(errorMessage);
            }

            const result = await response.json();
            
            if (result.success) {
                if (result.data && result.data.id) {
                    // Update last message ID immediately to prevent duplicate detection
                    const newMessageId = result.data.id;
                    this.lastMessageIds.set(this.activeConversationId, newMessageId);
                    
                    // Add the new message to the messages array without reloading all messages
                    const currentMessages = this.messages.get(this.activeConversationId) || [];
                    // Check if message already exists to prevent duplicates
                    const messageExists = currentMessages.some(msg => msg.id === newMessageId);
                    if (!messageExists) {
                        currentMessages.push(result.data);
                        this.messages.set(this.activeConversationId, currentMessages);
                        this.renderMessages(this.activeConversationId, currentMessages);
                    }
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError(error.message || 'Failed to send message');
        }
    }

    handleFileSelect(event) {
        const files = Array.from(event.target.files);
        this.pendingFiles = [...this.pendingFiles, ...files];
        this.showImagePreviews();
        event.target.value = '';
    }

    showImagePreviews() {
        const container = document.getElementById('chatImagePreviewContainer');
        const previewList = document.getElementById('chatImagePreviewList');
        
        if (!container || !previewList) return;

        if (this.pendingFiles.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        previewList.innerHTML = '';

        this.pendingFiles.forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'chat-image-preview-item';
            
            if (file.type.startsWith('image/')) {
                // Image preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button class="chat-image-preview-remove" data-index="${index}" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewList.appendChild(previewItem);
                    
                    const removeBtn = previewItem.querySelector('.chat-image-preview-remove');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', () => {
                            this.removeImagePreview(index);
                        });
                    }
                };
                reader.readAsDataURL(file);
            } else {
                // Document preview (PDF, DOC, DOCX)
                const fileIcon = this.getFileIcon(file.type, file.name);
                const fileSize = this.formatFileSize(file.size);
                previewItem.innerHTML = `
                    <div class="chat-file-preview">
                        <i class="${fileIcon}"></i>
                        <div class="chat-file-preview-info">
                            <span class="chat-file-preview-name">${this.escapeHtml(file.name)}</span>
                            <span class="chat-file-preview-size">${fileSize}</span>
                        </div>
                        <button class="chat-image-preview-remove" data-index="${index}" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewList.appendChild(previewItem);
                
                const removeBtn = previewItem.querySelector('.chat-image-preview-remove');
                if (removeBtn) {
                    removeBtn.addEventListener('click', () => {
                        this.removeImagePreview(index);
                    });
                }
            }
        });
    }

    removeImagePreview(index) {
        this.pendingFiles.splice(index, 1);
        this.showImagePreviews();
    }
    
    getFileIcon(mimeType, fileName) {
        if (mimeType === 'application/pdf' || fileName.toLowerCase().endsWith('.pdf')) {
            return 'fas fa-file-pdf';
        } else if (mimeType === 'application/msword' || 
                   mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                   fileName.toLowerCase().endsWith('.doc') || 
                   fileName.toLowerCase().endsWith('.docx')) {
            return 'fas fa-file-word';
        }
        return 'fas fa-file';
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    async markAsRead(conversationId) {
        try {
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/read`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                // Clear badge state from localStorage when messages are read
                try {
                    localStorage.removeItem('chatUnreadCount');
                    localStorage.removeItem('chatBadgeVisible');
                } catch (e) {
                    console.error('Error clearing badge state:', e);
                }
                
                // Update badge immediately if chat widget exists
                if (window.chatWidget && typeof window.chatWidget.updateUnreadCount === 'function') {
                    window.chatWidget.updateUnreadCount();
                }
            }
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    startConversationPolling(conversationId) {
        // Stop previous polling
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        if (this.reactionPollingInterval) {
            clearInterval(this.reactionPollingInterval);
            this.reactionPollingInterval = null;
        }

        // Poll for new messages every 2 seconds
        console.log('Started polling for conversation', conversationId);
        this.pollingInterval = setInterval(async () => {
            if (this.activeConversationId === conversationId) {
                await this.checkNewMessages(conversationId);
            }
        }, 2000);
        
        // Check for reaction changes every 5 seconds (less frequent to avoid too many API calls)
        this.reactionPollingInterval = setInterval(async () => {
            if (this.activeConversationId === conversationId) {
                await this.checkForReactionChangesInAllMessages(conversationId);
            }
        }, 5000);
    }

    async checkAllConversationsForNewMessages() {
        // Check all conversations for new messages to play sound notifications
        if (this.conversations && this.conversations.length > 0) {
            for (const conv of this.conversations) {
                // Only check conversations that have a last message ID (have been opened before)
                if (this.lastMessageIds.has(conv.id)) {
                    await this.checkNewMessages(conv.id, true); // Background check
                }
            }
        }
    }

    async checkNewMessages(conversationId, isBackgroundCheck = false) {
        const sinceId = this.lastMessageIds.get(conversationId) || 0;
        
        try {
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages/new?since=${sinceId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                if (!isBackgroundCheck) {
                    console.log('Failed to check new messages, response not ok');
                }
                return;
            }

            const data = await response.json();
            const newMessages = data.data || [];

            if (newMessages.length > 0) {
                console.log('checkNewMessages: Found', newMessages.length, 'new messages for conversation', conversationId);
                const currentMessages = this.messages.get(conversationId) || [];
                const existingIds = new Set(currentMessages.map(msg => msg.id));
                
                // Filter out any messages that already exist to prevent duplicates
                const uniqueNewMessages = newMessages.filter(msg => !existingIds.has(msg.id));
                
                if (uniqueNewMessages.length > 0) {
                    console.log('New messages detected:', uniqueNewMessages.length);
                    
                    // Check if any of the new messages are from other users (not the current user)
                    const user = window.currentUser || { id: null, type: null };
                    const company = window.currentCompany || { id: null };
                    
                    console.log('Current user:', user.id, 'Current company:', company.id);
                    console.log('New messages:', uniqueNewMessages.map(m => ({ id: m.id, sender_id: m.sender_id, sender_type: m.sender_type, has_reply_to: !!m.reply_to })));
                    
                    const hasIncomingMessage = uniqueNewMessages.some(msg => {
                        // Message is incoming if it's not from the current user/company
                        // This includes regular messages AND replies (replies are also messages)
                        if (user.id && msg.sender_type === 'user') {
                            const isIncoming = msg.sender_id !== user.id;
                            console.log('User message check - sender_id:', msg.sender_id, 'user.id:', user.id, 'isIncoming:', isIncoming, 'isReply:', !!msg.reply_to);
                            return isIncoming;
                        }
                        if (company.id && msg.sender_type === 'company') {
                            const isIncoming = msg.sender_id !== company.id;
                            console.log('Company message check - sender_id:', msg.sender_id, 'company.id:', company.id, 'isIncoming:', isIncoming, 'isReply:', !!msg.reply_to);
                            return isIncoming;
                        }
                        // If current user is a user and message is from company, it's incoming
                        if (user.id && msg.sender_type === 'company') {
                            console.log('User receiving company message - isIncoming: true, isReply:', !!msg.reply_to);
                            return true;
                        }
                        // If current user is a company and message is from user, it's incoming
                        if (company.id && msg.sender_type === 'user') {
                            console.log('Company receiving user message - isIncoming: true, isReply:', !!msg.reply_to);
                            return true;
                        }
                        console.log('Message type mismatch - isIncoming: false');
                        return false;
                    });
                    
                    console.log('Has incoming message (or reply):', hasIncomingMessage);
                    
                    // Play sound for incoming messages OR replies (from other users)
                    if (hasIncomingMessage) {
                        console.log('Playing notification sound for incoming message/reply...');
                        this.playNotificationSound();
                    } else {
                        console.log('Skipping sound - message is from current user');
                    }
                    
                    // Only update messages and render if this is the active conversation
                    // For background checks, we just want to play the sound
                    if (!isBackgroundCheck || this.activeConversationId === conversationId) {
                        this.messages.set(conversationId, [...currentMessages, ...uniqueNewMessages]);
                        this.lastMessageIds.set(conversationId, uniqueNewMessages[uniqueNewMessages.length - 1].id);
                        this.renderMessages(conversationId, this.messages.get(conversationId));
                    } else {
                        // For background conversations, just update the last message ID
                        this.lastMessageIds.set(conversationId, uniqueNewMessages[uniqueNewMessages.length - 1].id);
                    }
                }
            }
        } catch (error) {
            console.error('Error checking new messages:', error);
        }
    }
    
    playNotificationSound() {
        console.log('playNotificationSound called');
        try {
            // Use base URL if available
            const baseUrl = this.baseUrl || '';
            const soundPath = baseUrl + '/sounds/notification.mp3';
            console.log('Attempting to play sound from:', soundPath);
            
            // Try to play a sound file if it exists
            const audio = new Audio(soundPath);
            audio.volume = 0.7; // Increase volume slightly
            audio.preload = 'auto';
            
            // Add error handler
            audio.addEventListener('error', (e) => {
                console.error('Audio error:', e);
                console.log('Falling back to beep sound due to audio error');
                this.playBeepSound();
            });
            
            // Handle play promise with better error handling
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        // Sound played successfully
                        console.log('Notification sound played successfully');
                    })
                    .catch(error => {
                        // If sound file doesn't exist or autoplay blocked, generate a simple beep sound
                        console.log('Audio file play failed, using beep sound. Error:', error);
                        this.playBeepSound();
                    });
            } else {
                console.log('Play promise undefined, using beep sound');
                this.playBeepSound();
            }
        } catch (e) {
            // Fallback to beep sound if audio file fails
            console.log('Audio creation failed, using beep sound. Error:', e);
            this.playBeepSound();
        }
    }
    
    playBeepSound() {
        console.log('playBeepSound called');
        try {
            // Create or resume audio context (browsers require user interaction first)
            let audioContext = this.audioContext;
            if (!audioContext) {
                console.log('Creating new audio context');
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                this.audioContext = audioContext;
            }
            
            console.log('Audio context state:', audioContext.state);
            
            // Resume audio context if suspended (required after user interaction)
            if (audioContext.state === 'suspended') {
                console.log('Resuming suspended audio context');
                audioContext.resume().then(() => {
                    console.log('Audio context resumed, playing beep');
                    this.playBeepSoundInternal(audioContext);
                }).catch((error) => {
                    console.log('Could not resume audio context:', error);
                });
            } else {
                console.log('Audio context ready, playing beep');
                this.playBeepSoundInternal(audioContext);
            }
        } catch (e) {
            // Web Audio API not supported
            console.log('Beep sound not available:', e);
        }
    }
    
    playBeepSoundInternal(audioContext) {
        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800; // Frequency in Hz
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.4, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        } catch (e) {
            console.log('Beep sound generation failed:', e);
        }
    }
    
    initializeAudio() {
        // Initialize audio context on first user interaction (required by browsers)
        const initAudio = () => {
            if (!this.audioContext) {
                try {
                    this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    // Resume audio context if suspended
                    if (this.audioContext.state === 'suspended') {
                        this.audioContext.resume();
                    }
                } catch (e) {
                    console.log('Audio context initialization failed:', e);
                }
            }
        };
        
        // Initialize on any user interaction
        ['click', 'touchstart', 'keydown'].forEach(event => {
            document.addEventListener(event, initAudio, { once: true });
        });
    }

    startPolling() {
        // Poll for conversation updates every 5 seconds
        setInterval(() => {
            this.loadConversations(this.currentFilter, this.selectedJobId);
        }, 5000);
        
        // Also check for new messages in all conversations every 3 seconds for sound notifications
        setInterval(() => {
            this.checkAllConversationsForNewMessages();
        }, 3000);
        
        // Poll for status updates every 30 seconds
        setInterval(() => {
            this.updateUserStatuses();
        }, 30000);
    }
    
    async updateUserStatuses() {
        // Update status indicators for all conversations
        this.conversations.forEach(conv => {
            const participant = conv.other_participant || conv.company || conv.user;
            if (!participant || !participant.id || !participant.type) return;
            
            // Fetch status for this participant
            fetch(`${this.baseUrl}/chat/status/${participant.id}/${participant.type}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    let status = data.data.status || 'offline';
                    // Check if user is actually online (active within last 5 minutes)
                    if (status === 'online' && data.data.last_activity_at) {
                        const lastActivity = new Date(data.data.last_activity_at);
                        const minutesAgo = (Date.now() - lastActivity.getTime()) / 60000;
                        if (minutesAgo > 5) {
                            status = 'offline';
                        }
                    }
                    
                    // Update status in conversation data
                    if (participant) {
                        participant.status = status;
                    }
                    
                    // Update status indicator in UI
                    const statusIndicator = document.querySelector(`[data-conversation-id="${conv.id}"] .chat-status-indicator`);
                    if (statusIndicator) {
                        statusIndicator.className = `chat-status-indicator chat-status-${status}`;
                    }
                    
                    // Update header status if this is the active conversation
                    if (this.activeConversationId === conv.id) {
                        const headerStatus = document.getElementById('chatHeaderStatus');
                        if (headerStatus) {
                            headerStatus.className = `chat-status-indicator chat-status-${status}`;
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
            });
        });
    }

    async updateActivity() {
        try {
            await fetch(this.baseUrl + '/chat/status/activity', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });
        } catch (error) {
            console.error('Error updating activity:', error);
        }
    }

    filterConversations(searchTerm) {
        const items = document.querySelectorAll('.chat-list-item');
        const term = searchTerm.toLowerCase();

        items.forEach(item => {
            const name = item.querySelector('.chat-list-item-name')?.textContent.toLowerCase() || '';
            const message = item.querySelector('.chat-list-item-message')?.textContent.toLowerCase() || '';
            
            if (name.includes(term) || message.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    scrollToBottom() {
        const container = document.querySelector('.chat-messages-container');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        
        if (minutes < 1) return window.CHAT_TRANSLATIONS?.justNow || 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h ago`;
        
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    formatUserStatus(status, lastSeenAt) {
        if (status === 'online') {
            return 'Online';
        }
        
        if (!lastSeenAt) {
            return 'Offline';
        }
        
        const lastSeen = new Date(lastSeenAt);
        const now = new Date();
        const diffMs = now - lastSeen;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        const diffWeeks = Math.floor(diffMs / 604800000);
        const diffMonths = Math.floor(diffMs / 2592000000);
        
        if (diffMins < 1) {
            return 'Offline - Last seen just now';
        } else if (diffMins < 60) {
            return `Offline - Last seen ${diffMins} ${diffMins === 1 ? 'minute' : 'minutes'} ago`;
        } else if (diffHours < 24) {
            return `Offline - Last seen ${diffHours} ${diffHours === 1 ? 'hour' : 'hours'} ago`;
        } else if (diffDays < 7) {
            return `Offline - Last seen ${diffDays} ${diffDays === 1 ? 'day' : 'days'} ago`;
        } else if (diffWeeks < 4) {
            return `Offline - Last seen ${diffWeeks} ${diffWeeks === 1 ? 'week' : 'weeks'} ago`;
        } else if (diffMonths < 12) {
            return `Offline - Last seen ${diffMonths} ${diffMonths === 1 ? 'month' : 'months'} ago`;
        } else {
            // Show actual date if more than a year
            return `Offline - Last seen ${lastSeen.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
        }
    }

    getProfileLink(participant) {
        if (!participant) return '';
        
        const name = this.escapeHtml(participant.name || 'Unknown');
        
        // Check if participant is a company
        if (participant.type === 'company' && participant.slug) {
            const profileUrl = `${this.baseUrl}/company/${participant.slug}`;
            return `<a href="${profileUrl}" target="_blank" rel="noopener noreferrer" class="chat-profile-link" onclick="event.stopPropagation();">${name}</a>`;
        }
        
        // Check if participant is a user
        if (participant.type === 'user' && participant.id) {
            const profileUrl = `${this.baseUrl}/view-public-profile/${participant.id}`;
            return `<a href="${profileUrl}" target="_blank" rel="noopener noreferrer" class="chat-profile-link" onclick="event.stopPropagation();">${name}</a>`;
        }
        
        // Fallback: return plain text if no profile link available
        return name;
    }

    initMessageSearch() {
        const searchBtn = document.getElementById('chatHeaderSearchBtn');
        const searchOverlay = document.getElementById('chatSearchOverlay');
        const searchInput = document.getElementById('chatMessageSearchInput');
        const searchCloseBtn = document.getElementById('chatSearchCloseBtn');
        const searchResults = document.getElementById('chatSearchResults');
        
        let searchTimeout = null;
        
        // Open search overlay
        if (searchBtn && searchOverlay) {
            searchBtn.addEventListener('click', () => {
                if (!this.activeConversationId) {
                    alert('Please select a conversation first');
                    return;
                }
                searchOverlay.style.display = 'block';
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 100);
                }
            });
        }
        
        // Close search overlay
        if (searchCloseBtn && searchOverlay) {
            searchCloseBtn.addEventListener('click', () => {
                searchOverlay.style.display = 'none';
                if (searchInput) {
                    searchInput.value = '';
                }
                if (searchResults) {
                    searchResults.innerHTML = '<div class="chat-search-empty">' + (window.CHAT_TRANSLATIONS?.typeToSearch || 'Type to search messages...') + '</div>';
                }
                // Reload messages without search
                if (this.activeConversationId) {
                    this.loadMessages(this.activeConversationId);
                }
            });
        }
        
        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchOverlay && searchOverlay.style.display === 'block') {
                searchOverlay.style.display = 'none';
                if (searchInput) {
                    searchInput.value = '';
                }
                if (searchResults) {
                    searchResults.innerHTML = '<div class="chat-search-empty">' + (window.CHAT_TRANSLATIONS?.typeToSearch || 'Type to search messages...') + '</div>';
                }
                if (this.activeConversationId) {
                    this.loadMessages(this.activeConversationId);
                }
            }
        });
        
        // Search input handler
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                
                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                if (!query) {
                    if (searchResults) {
                        searchResults.innerHTML = '<div class="chat-search-empty">' + (window.CHAT_TRANSLATIONS?.typeToSearch || 'Type to search messages...') + '</div>';
                    }
                    // Reload messages without search
                    if (this.activeConversationId) {
                        this.loadMessages(this.activeConversationId);
                    }
                    return;
                }
                
                // Debounce search
                searchTimeout = setTimeout(() => {
                    this.searchMessages(query);
                }, 300);
            });
        }
    }

    async searchMessages(query) {
        if (!this.activeConversationId || !query.trim()) {
            return;
        }
        
        const searchResults = document.getElementById('chatSearchResults');
        if (!searchResults) return;
        
        try {
            // Show loading
            searchResults.innerHTML = '<div class="chat-search-loading">Searching...</div>';
            
            // Load messages with search query
            const messages = await this.loadMessages(this.activeConversationId, query);
            
            if (!messages || messages.length === 0) {
                searchResults.innerHTML = '<div class="chat-search-empty">No messages found</div>';
                return;
            }
            
            // Display search results
            const resultsHtml = messages.map(msg => {
                const date = this.formatDate(msg.created_at);
                const time = new Date(msg.created_at).toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
                const messageText = this.highlightSearchTerm(this.escapeHtml(msg.message || ''), query);
                
                return `
                    <div class="chat-search-result-item" data-message-id="${msg.id}">
                        <div class="chat-search-result-time">${date} at ${time}</div>
                        <div class="chat-search-result-message">${messageText}</div>
                    </div>
                `;
            }).join('');
            
            searchResults.innerHTML = resultsHtml;
            
            // Add click handler to scroll to message
            searchResults.querySelectorAll('.chat-search-result-item').forEach(item => {
                item.addEventListener('click', () => {
                    const messageId = parseInt(item.dataset.messageId);
                    // Close search overlay first
                    const searchOverlay = document.getElementById('chatSearchOverlay');
                    const searchInput = document.getElementById('chatMessageSearchInput');
                    if (searchOverlay) {
                        searchOverlay.style.display = 'none';
                    }
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    // Then scroll to message (this will reload all messages first)
                    this.scrollToMessage(messageId);
                });
            });
            
        } catch (error) {
            console.error('Error searching messages:', error);
            searchResults.innerHTML = '<div class="chat-search-error">Error searching messages</div>';
        }
    }

    highlightSearchTerm(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${this.escapeRegex(query)})`, 'gi');
        return text.replace(regex, '<mark class="chat-search-highlight">$1</mark>');
    }

    escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    scrollToMessage(messageId) {
        // First, reload all messages (without search) to ensure the message is in the DOM
        this.loadMessages(this.activeConversationId, '').then(() => {
            // Wait a bit for DOM to update
            setTimeout(() => {
                // Try to find the message element - use more specific selector
                const messageElement = document.querySelector(`.chat-message[data-message-id="${messageId}"]`);
                if (messageElement) {
                    // Scroll to the message
                    messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Highlight the message temporarily
                    messageElement.classList.add('chat-message-highlighted');
                    setTimeout(() => {
                        messageElement.classList.remove('chat-message-highlighted');
                    }, 2000);
                } else {
                    // If still not found, try again after a longer delay
                    setTimeout(() => {
                        const msgEl = document.querySelector(`.chat-message[data-message-id="${messageId}"]`);
                        if (msgEl) {
                            msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            msgEl.classList.add('chat-message-highlighted');
                            setTimeout(() => {
                                msgEl.classList.remove('chat-message-highlighted');
                            }, 2000);
                        } else {
                            console.warn('Message element not found:', messageId);
                        }
                    }, 500);
                }
            }, 200);
        }).catch(error => {
            console.error('Error loading messages for scroll:', error);
        });
    }

    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const days = Math.floor(diff / 86400000);
        
        if (days === 0) return 'Today';
        if (days === 1) return 'Yesterday';
        if (days < 7) return `${days}d ago`;
        
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showError(message) {
        console.error(message);
        // TODO: Show user-friendly error message
    }

    // Reaction methods - now inline in footer, no separate popup needed
    // The reaction picker is already in the footer HTML, just need to handle clicks

    async toggleReaction(messageId, emoji) {
        try {
            // Store current reactions before update to detect changes from others
            const currentMessages = this.messages.get(this.activeConversationId) || [];
            const currentMessage = currentMessages.find(m => m.id == messageId);
            const oldReactions = currentMessage ? (currentMessage.reactions || []) : [];
            
            const response = await fetch(`${this.baseUrl}/chat/messages/${messageId}/reaction`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: JSON.stringify({ emoji: emoji })
            });

            if (!response.ok) throw new Error('Failed to toggle reaction');

            const result = await response.json();
            if (result.success) {
                // Reload messages to show updated reactions
                if (this.activeConversationId) {
                    await this.loadMessages(this.activeConversationId);
                    
                    // After reload, check if reactions changed (someone else reacted)
                    // Note: This will also check when we reload messages via polling
                    setTimeout(() => {
                        this.checkForReactionChanges(messageId, oldReactions);
                    }, 100);
                }
            }
        } catch (error) {
            console.error('Error toggling reaction:', error);
        }
    }
    
    checkForReactionChanges(messageId, oldReactions) {
        // Check if reactions changed due to someone else's action
        const currentMessages = this.messages.get(this.activeConversationId) || [];
        const currentMessage = currentMessages.find(m => m.id == messageId);
        
        if (!currentMessage || !currentMessage.reactions) return false;
        
        const newReactions = currentMessage.reactions || [];
        const user = window.currentUser || { id: null, type: null };
        const company = window.currentCompany || { id: null };
        
        // Check if any new reactions are from other users
        const hasNewReactionFromOthers = newReactions.some(newReaction => {
            // Check if this reaction exists in old reactions
            const oldReaction = oldReactions.find(r => r.emoji === newReaction.emoji);
            const oldCount = oldReaction ? oldReaction.count : 0;
            const newCount = newReaction.count || 0;
            
            // If count increased, check if it's from someone else
            if (newCount > oldCount) {
                // Check if current user is in the reaction users list
                const isCurrentUserReaction = newReaction.users && newReaction.users.some(u => 
                    (user.id && u.id === user.id && u.type === 'user') ||
                    (company.id && u.id === company.id && u.type === 'company')
                );
                
                // If count increased and it's not from current user, play sound
                if (!isCurrentUserReaction) {
                    console.log('New reaction detected from other user:', newReaction.emoji);
                    return true;
                }
            }
            return false;
        });
        
        if (hasNewReactionFromOthers) {
            console.log('Playing notification sound for reaction from other user');
            this.playNotificationSound();
            return true;
        }
        return false;
    }
    
    async checkForReactionChangesInAllMessages(conversationId) {
        // Periodically reload messages to check for reaction changes
        // This ensures we catch reactions from others even if they don't send new messages
        try {
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const data = await response.json();
            const messages = data.data || [];
            
            // Store old reactions
            const currentMessages = this.messages.get(conversationId) || [];
            const oldReactionsMap = new Map();
            currentMessages.forEach(msg => {
                if (msg.reactions && msg.reactions.length > 0) {
                    oldReactionsMap.set(msg.id, msg.reactions);
                }
            });
            
            // Check each message for reaction changes
            let hasReactionChange = false;
            messages.forEach(msg => {
                if (msg.reactions && msg.reactions.length > 0) {
                    const oldReactions = oldReactionsMap.get(msg.id) || [];
                    if (this.checkForReactionChanges(msg.id, oldReactions)) {
                        hasReactionChange = true;
                    }
                }
            });
            
            // Update messages if reactions changed (but don't re-render to avoid flicker)
            if (hasReactionChange) {
                this.messages.set(conversationId, messages);
                // Re-render to show updated reactions
                this.renderMessages(conversationId, messages);
            }
        } catch (error) {
            // Silently fail - this is a background check
            console.log('Error checking reaction changes:', error);
        }
    }

    // Reply methods
    startReply(messageId) {
        this.replyingToMessageId = messageId;
        this.updateReplyPreview();
        
        // Scroll to input and focus
        const input = document.getElementById('chatMessageInput');
        if (input) {
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    cancelReply() {
        this.replyingToMessageId = null;
        this.updateReplyPreview();
    }
    
    startEdit(messageId) {
        console.log('startEdit called with messageId:', messageId);
        const messages = this.messages.get(this.activeConversationId) || [];
        const message = messages.find(m => m.id == messageId);
        
        console.log('Found message:', message);
        
        if (!message) {
            console.error('Message not found:', messageId);
            return;
        }
        
        if (message.message_type !== 'text') {
            console.log('Cannot edit non-text message');
            return;
        }
        
        if (message.is_deleted) {
            console.log('Cannot edit deleted message');
            return;
        }
        
        this.editingMessageId = messageId;
        this.replyingToMessageId = null; // Clear reply when editing
        
        // Update input with message text
        const input = document.getElementById('chatMessageInput');
        if (input) {
            input.value = message.message || '';
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }
        
        // Update input placeholder and show edit indicator
        this.updateEditPreview();
        console.log('Edit mode activated for message:', messageId);
    }
    
    cancelEdit() {
        this.editingMessageId = null;
        const input = document.getElementById('chatMessageInput');
        if (input) {
            input.value = '';
        }
        this.updateEditPreview();
    }
    
    updateEditPreview() {
        const inputContainer = document.querySelector('.chat-input-container');
        if (!inputContainer) return;
        
        // Remove existing edit preview
        const existingPreview = inputContainer.querySelector('.chat-edit-preview');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        // Add preview if editing
        if (this.editingMessageId) {
            const messages = this.messages.get(this.activeConversationId) || [];
            const message = messages.find(m => m.id == this.editingMessageId);
            
            if (message) {
                const preview = document.createElement('div');
                preview.className = 'chat-edit-preview';
                const previewText = message.message.length > 50 ? message.message.substring(0, 50) + '...' : message.message;
                
                preview.innerHTML = `
                    <div class="chat-edit-preview-content">
                        <i class="fas fa-edit"></i>
                        <span>Editing: ${this.escapeHtml(previewText)}</span>
                    </div>
                    <button class="chat-edit-cancel" onclick="fullPageChat.cancelEdit()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                inputContainer.insertBefore(preview, inputContainer.firstChild);
            }
        }
    }
    
    async deleteMessage(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) {
            return;
        }
        
        console.log('Deleting message', messageId);
        try {
            const response = await fetch(`${this.baseUrl}/chat/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Failed to delete message' }));
                throw new Error(errorData.message || 'Failed to delete message');
            }

            const result = await response.json();
            console.log('Delete response:', result);
            
            if (result.success) {
                // Reload messages to show deleted status
                if (this.activeConversationId) {
                    await this.loadMessages(this.activeConversationId);
                }
            } else {
                throw new Error(result.message || 'Failed to delete message');
            }
        } catch (error) {
            console.error('Error deleting message:', error);
            this.showError(error.message || 'Failed to delete message');
        }
    }

    updateReplyPreview() {
        const inputContainer = document.querySelector('.chat-input-container');
        if (!inputContainer) return;

        // Remove existing preview
        const existingPreview = inputContainer.querySelector('.chat-reply-preview');
        if (existingPreview) {
            existingPreview.remove();
        }

        // Add preview if replying
        if (this.replyingToMessageId) {
            const messages = this.messages.get(this.activeConversationId) || [];
            const message = messages.find(m => m.id == this.replyingToMessageId);
            
            if (message) {
                const preview = document.createElement('div');
                preview.className = 'chat-reply-preview';
                const previewText = message.message_type === 'text' 
                    ? (message.message.length > 50 ? message.message.substring(0, 50) + '...' : message.message)
                    : (message.message_type === 'image' ? '📷 Image' : '📎 File');
                
                preview.innerHTML = `
                    <div class="chat-reply-preview-content">
                        <i class="fas fa-reply"></i>
                        <span>${this.escapeHtml(previewText)}</span>
                    </div>
                    <button class="chat-reply-cancel" onclick="fullPageChat.cancelReply()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                inputContainer.insertBefore(preview, inputContainer.firstChild);
            }
        }
    }

    // Typing Indicator Methods
    handleTyping(conversationId) {
        if (!conversationId) return;
        
        // Send typing indicator
        this.sendTypingIndicator(conversationId, true);
        
        // Clear existing timeout
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }
        
        // Stop typing indicator after 3 seconds of inactivity
        this.typingTimeout = setTimeout(() => {
            this.sendTypingIndicator(conversationId, false);
        }, 3000);
    }

    async sendTypingIndicator(conversationId, isTyping) {
        if (!conversationId) return;
        
        try {
            const user = window.currentUser || { id: null, type: null };
            const company = window.currentCompany || { id: null };
            
            if (!user.id && !company.id) return;

            const response = await fetch(`${this.baseUrl}/chat/status/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    conversation_id: conversationId,
                    is_typing: isTyping
                })
            });

            if (!response.ok) {
                // Only log if it's not a 401 (unauthorized) - those are expected if user is not authenticated
                if (response.status !== 401 && response.status !== 500) {
                    console.warn('Typing indicator send failed:', response.status, response.statusText);
                }
            }
        } catch (error) {
            // Silently fail - typing indicator is not critical
            // Only log if it's not a network error
            if (error.name !== 'TypeError') {
                console.error('Error sending typing indicator:', error);
            }
        }
    }

    async checkTypingStatus(conversationId) {
        if (!conversationId) return;
        
        try {
            const user = window.currentUser || { id: null, type: null };
            const company = window.currentCompany || { id: null };
            
            if (!user.id && !company.id) return;

            const response = await fetch(`${this.baseUrl}/chat/status/typing/${conversationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                // Only log if it's not a 401 (unauthorized) - those are expected if user is not authenticated
                if (response.status !== 401) {
                    console.warn('Typing status check failed:', response.status, response.statusText);
                }
                return;
            }

            const result = await response.json();
            
            if (result.success && result.data && result.data.is_typing) {
                const participant = result.data.user;
                this.showTypingIndicator(participant.name || 'Someone');
            } else {
                this.hideTypingIndicator();
            }
        } catch (error) {
            // Silently fail - typing indicator is not critical
            // Only log if it's not a network error
            if (error.name !== 'TypeError') {
                console.error('Error checking typing status:', error);
            }
        }
    }

    showTypingIndicator(name) {
        const indicator = document.getElementById('chatTypingIndicator');
        const typingText = document.getElementById('typingText');
        
        if (indicator) {
            if (typingText) {
                typingText.textContent = `${name} ${window.CHAT_TRANSLATIONS?.isTyping || 'is typing...'}`;
            }
            // Use both style and class to ensure visibility
            indicator.style.display = 'flex';
            indicator.style.visibility = 'visible';
            indicator.style.opacity = '1';
            console.log('Typing indicator shown for:', name, indicator);
        } else {
            console.error('Typing indicator element not found');
        }
    }

    hideTypingIndicator() {
        const indicator = document.getElementById('chatTypingIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    startTypingPolling(conversationId) {
        // Stop previous polling
        if (this.typingPollingInterval) {
            clearInterval(this.typingPollingInterval);
        }
        
        // Poll for typing status every 2 seconds
        this.typingPollingInterval = setInterval(() => {
            if (this.activeConversationId === conversationId) {
                this.checkTypingStatus(conversationId);
            } else {
                clearInterval(this.typingPollingInterval);
                this.typingPollingInterval = null;
            }
        }, 2000);
    }

    initEmojiPicker() {
        const emojiPickerContent = document.getElementById('emojiPickerContent');
        if (!emojiPickerContent) return;

        // Always render emojis (in case content was cleared)
        // Common emojis
        const emojis = [
            '😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇',
            '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚',
            '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩',
            '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣',
            '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬',
            '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗',
            '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😬', '🙄', '😯',
            '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐',
            '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '👏', '🙌', '👐',
            '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔',
            '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️',
            '✝️', '☪️', '🕉', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐',
            '⛎', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐',
            '♑', '♒', '♓', '🆔', '⚛️', '🉑', '☢️', '☣️', '📴', '📳',
            '🈶', '🈚', '🈸', '🈺', '🈷️', '✴️', '🆚', '💮', '🉐', '㊙️',
            '㊗️', '🈴', '🈵', '🈹', '🈲', '🅰️', '🅱️', '🆎', '🆑', '🅾️',
            '🆘', '❌', '⭕', '🛑', '⛔', '📛', '🚫', '💯', '💢', '♨️',
            '🚷', '🚯', '🚳', '🚱', '🔞', '📵', '🚭', '❗', '❕', '❓',
            '❔', '‼️', '⁉️', '🔅', '🔆', '〽️', '⚠️', '🚸', '🔱', '⚜️',
            '🔰', '♻️', '✅', '🈯', '💹', '❇️', '✳️', '❎', '🌐', '💠',
            'Ⓜ️', '🌀', '💤', '🏧', '🚾', '♿', '🅿️', '🈳', '🈂️', '🛂',
            '🛃', '🛄', '🛅', '🚹', '🚺', '🚼', '🚻', '🚮', '🎦', '📶',
            '🈁', '🔣', 'ℹ️', '🔤', '🔡', '🔠', '🆖', '🆗', '🆙', '🆒',
            '🆕', '🆓', '0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣',
            '8️⃣', '9️⃣', '🔟', '🔢', '#️⃣', '*️⃣', '▶️', '⏸', '⏯', '⏹',
            '⏺', '⏭', '⏮', '⏩', '⏪', '⏫', '⏬', '◀️', '🔼', '🔽',
            '➡️', '⬅️', '⬆️', '⬇️', '↗️', '↘️', '↙️', '↖️', '↕️', '↔️',
            '↪️', '↩️', '⤴️', '⤵️', '🔀', '🔁', '🔂', '🔄', '🔃', '🎵',
            '🎶', '➕', '➖', '➗', '✖️', '💲', '💱', '™️', '©️', '®️',
            '〰️', '➰', '➿', '🔚', '🔙', '🔛', '🔜', '🔝', '✔️', '☑️',
            '🔘', '⚪', '⚫', '🔴', '🔵', '🟠', '🟡', '🟢', '🟣', '🟤',
            '⚫', '⬜', '⬛', '🟧', '🟨', '🟩', '🟦', '🟪', '🟫', '🔶',
            '🔷', '🔸', '🔹', '🔺', '🔻', '💠', '🔘', '🔳', '🔲', '▪️',
            '▫️', '◾', '◽', '◼️', '◻️', '🟥', '🟧', '🟨', '🟩', '🟦',
            '🟪', '🟫', '⬛', '⬜', '🔈', '🔇', '🔉', '🔊', '🔔', '🔕',
            '📣', '📢', '👁‍🗨', '💬', '💭', '🗯', '♠️', '♣️', '♥️', '♦️',
            '🃏', '🎴', '🀄', '🕐', '🕑', '🕒', '🕓', '🕔', '🕕', '🕖',
            '🕗', '🕘', '🕙', '🕚', '🕛', '🕜', '🕝', '🕞', '🕟', '🕠',
            '🕡', '🕢', '🕣', '🕤', '🕥', '🕦', '🕧'
        ];

        // Don't escape emojis - they need to be raw
        emojiPickerContent.innerHTML = emojis.map(emoji => 
            `<span class="emoji-item" data-emoji="${emoji}">${emoji}</span>`
        ).join('');

        // Use event delegation - only attach listener once
        if (emojiPickerContent.dataset.listenerAttached !== 'true') {
            emojiPickerContent.addEventListener('click', (e) => {
                const emojiItem = e.target.closest('.emoji-item');
                if (!emojiItem) return;
                
                e.preventDefault();
                e.stopPropagation();
                
                // Get emoji from data attribute or text content (fallback)
                let emoji = emojiItem.getAttribute('data-emoji');
                if (!emoji || emoji.trim() === '') {
                    emoji = emojiItem.textContent || emojiItem.innerText || '';
                }
                if (!emoji || emoji.trim() === '') {
                    console.warn('No emoji found');
                    return;
                }
                
                const input = document.getElementById('chatMessageInput');
                if (!input) {
                    console.warn('Input field not found');
                    return;
                }
                
                // Get current cursor position (works for both input and textarea)
                const cursorPos = input.selectionStart || input.value.length;
                const currentValue = input.value || '';
                const textBefore = currentValue.substring(0, cursorPos);
                const textAfter = currentValue.substring(cursorPos);
                
                // Insert emoji at cursor position
                const newValue = textBefore + emoji + textAfter;
                input.value = newValue;
                
                // Set cursor position after emoji
                const newCursorPos = cursorPos + emoji.length;
                
                // Focus and set cursor position
                input.focus();
                
                // Use setTimeout to ensure DOM is updated
                setTimeout(() => {
                    try {
                        input.setSelectionRange(newCursorPos, newCursorPos);
                    } catch (e) {
                        // Fallback if setSelectionRange fails
                        input.focus();
                    }
                    
                    // Trigger input event for auto-resize (textarea) and validation
                    const inputEvent = new Event('input', { bubbles: true, cancelable: true });
                    input.dispatchEvent(inputEvent);
                    
                    // Also trigger change event to ensure value is registered
                    const changeEvent = new Event('change', { bubbles: true, cancelable: true });
                    input.dispatchEvent(changeEvent);
                    
                    // Debug: Log the value to verify emoji is inserted
                    console.log('Emoji inserted, input value:', input.value);
                }, 10);
                
                // Close emoji picker
                const emojiPicker = document.getElementById('chatEmojiPicker');
                if (emojiPicker) {
                    emojiPicker.style.display = 'none';
                }
            });
        }
        
        // Mark listener as attached
        emojiPickerContent.dataset.listenerAttached = 'true';
    }

    // Mobile view management
    isMobile() {
        return window.innerWidth <= 768;
    }

    showMobileChatView() {
        if (!this.isMobile()) return;
        
        const sidebar = document.querySelector('.chat-sidebar');
        const mainChat = document.querySelector('.chat-main');
        
        if (sidebar) {
            sidebar.classList.add('chat-mobile-hidden');
        }
        if (mainChat) {
            mainChat.classList.add('chat-mobile-active');
        }
    }

    showMobileConversationList() {
        if (!this.isMobile()) return;
        
        const sidebar = document.querySelector('.chat-sidebar');
        const mainChat = document.querySelector('.chat-main');
        
        if (sidebar) {
            sidebar.classList.remove('chat-mobile-hidden');
        }
        if (mainChat) {
            mainChat.classList.remove('chat-mobile-active');
        }
        
        // Clear active conversation
        this.activeConversationId = null;
        this.activeConversation = null;
        
        // Hide typing indicator
        this.hideTypingIndicator();
        
        // Stop polling
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        if (this.typingPollingInterval) {
            clearInterval(this.typingPollingInterval);
            this.typingPollingInterval = null;
        }
        
        // Show empty state
        document.getElementById('chatEmptyState').style.display = 'flex';
        document.getElementById('chatActiveView').style.display = 'none';
        
        // Remove active state from conversation items
        document.querySelectorAll('.chat-list-item').forEach(item => {
            item.classList.remove('active');
        });
    }
}

// Initialize when DOM is ready
let fullPageChat;
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if user is authenticated
    const user = window.currentUser || { id: null, type: null };
    const company = window.currentCompany || { id: null };
    
    if (user.id || company.id) {
        fullPageChat = new FullPageChat();
        window.fullPageChat = fullPageChat;
    }
});

})();

