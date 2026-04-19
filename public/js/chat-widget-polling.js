/**
 * Instant Chat Widget Manager - Self-Hosted (No WebSockets)
 * Uses AJAX polling for real-time feel
 */

(function() {
"use strict";

class ChatWidget {
    constructor() {
        this.isOpen = false;
        this.isMinimized = false;
        this.activeConversations = new Map();
        this.unreadCount = 0;
        this.pendingFiles = new Map();
        this.pollingIntervals = new Map(); // Track polling for each conversation
        this.lastMessageIds = new Map(); // Track last message ID per conversation
        this.allConversations = []; // Store all conversations for global polling
        this.globalPollingInterval = null; // Global polling for all conversations
        this.storedReactionsMap = new Map(); // Store reactions to detect changes
        this.lastBadgeCount = 0; // Track last badge count to prevent premature hiding
        this.badgeHideTimeout = null; // Timeout to delay badge hiding
        this.zeroCountConfirmations = 0; // Count how many times we've seen 0 (to confirm it's real)
        this.baseUrl = window.CHAT_BASE_URL || ''; // Get base URL from window variable
        this.audioContext = null; // For beep sound generation
        this.init();
    }

    init() {
        // Restore badge state from localStorage on page load
        this.restoreBadgeState();

        // Always run badge updates when the header badge exists (user is logged in)
        // Server already renders initial count in the badge for instant display; we poll to keep it updated
        const badgeEl = document.getElementById('chatUnreadBadge');
        if (badgeEl) {
            const initialCount = parseInt(badgeEl.getAttribute('data-initial-count') || '0', 10);
            if (initialCount > 0) {
                this.unreadCount = initialCount;
                this.lastBadgeCount = initialCount;
            }
            this.updateUnreadCount(); // Refresh from server
            setInterval(() => this.updateUnreadCount(), 5000); // Poll every 5 seconds
        }

        // Check if full widget exists before initializing dropdown, conversations, etc.
        const widget = document.getElementById('instantChatWidget');
        if (!widget) {
            return; // Don't initialize if widget doesn't exist (user not logged in)
        }

        this.setupEventListeners();
        this.initializeAudio(); // Initialize audio on user interaction
        this.loadConversations();
        this.updateUnreadCount();
        this.startStatusPolling();
        this.updateActivity();
        this.startGlobalPolling(); // Start polling all conversations for notifications
        
        // Update chat box positions on window resize
        window.addEventListener('resize', () => {
            this.updateChatBoxPositions();
        });
    }

    setupEventListeners() {
        const toggleBtn = document.getElementById('chatToggleBtn');
        if (toggleBtn) {
            // Toggle dropdown on click instead of opening widget
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleHeaderDropdown();
            });
        }

        const minimizeBtn = document.getElementById('chatMinimizeBtn');
        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', () => this.minimizeWidget());
        }

        const closeBtn = document.getElementById('chatCloseBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeWidget());
        }

        const searchInput = document.getElementById('chatSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.searchConversations(e.target.value));
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('chatHeaderDropdown');
            const toggleBtn = document.getElementById('chatToggleBtn');
            if (dropdown && toggleBtn && !dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
                this.closeHeaderDropdown();
            }
        });
    }

    async loadConversations() {
        // Check if widget exists (user is authenticated)
        const widget = document.getElementById('instantChatWidget');
        if (!widget) {
            return; // Don't load if widget doesn't exist (user not logged in)
        }

        try {
            const response = await fetch(this.baseUrl + '/chat/conversations', {
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
            const conversations = data.data || [];
            this.allConversations = conversations; // Store for global polling
            
            // Initialize lastMessageId for conversations that have messages
            conversations.forEach(conv => {
                if (conv.last_message && !this.lastMessageIds.has(conv.id)) {
                    // Set initial lastMessageId to 0 so we check all recent messages on first poll
                    // This will be updated when we actually check for new messages
                    this.lastMessageIds.set(conv.id, 0);
                }
            });
            
            this.renderConversations(conversations);
            this.updateUnreadCount();
        } catch (error) {
            // Only show error if widget exists (user is logged in)
            const widget = document.getElementById('instantChatWidget');
            if (widget) {
                console.error('Error loading conversations:', error);
                // Don't show alert on login page
                if (!window.location.pathname.includes('login') && !window.location.pathname.includes('register')) {
                    this.showError('Failed to load conversations');
                }
            }
        }
    }

    renderConversations(conversations) {
        const container = document.getElementById('chatConversations');
        if (!container) return;

        if (conversations.length === 0) {
            container.innerHTML = '<div class="chat-empty-state">No conversations yet</div>';
            return;
        }

        container.innerHTML = conversations.map(conv => {
            const lastMsg = conv.last_message;
            const preview = lastMsg ? (lastMsg.message_type === 'image' ? '📷 Image' : 
                                      lastMsg.message_type === 'file' ? '📎 File' : 
                                      lastMsg.message) : 'No messages yet';
            
            // Use other_participant which is always available
            const participant = conv.other_participant || conv.company || conv.user;
            if (!participant) return ''; // Skip if no participant
            
            const participantId = participant.id;
            const participantName = participant.name || 'Unknown';
            const participantLogo = participant.logo || '/images/default-user.png';
            
            return `
                <div class="chat-conversation-item" 
                     data-conversation-id="${conv.id}"
                     onclick="chatWidget.openChatBox(${conv.id}, ${participantId}, '${this.escapeHtml(participantName)}', '${participantLogo}')">
                    <div class="chat-conversation-avatar">
                        <img src="${participantLogo}" alt="${this.escapeHtml(participantName)}">
                        <span class="chat-status-indicator chat-status-online"></span>
                    </div>
                    <div class="chat-conversation-info">
                        <div class="chat-conversation-name">${this.escapeHtml(participantName)}</div>
                        <div class="chat-conversation-preview">${this.escapeHtml(preview)}</div>
                    </div>
                    <div class="chat-conversation-meta">
                        ${conv.unread_count > 0 ? `<div class="chat-conversation-unread">${conv.unread_count}</div>` : ''}
                        <div class="chat-conversation-time">${this.formatTime(conv.last_message_at)}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async openChat(conversationId) {
        // Load conversation details and open chat
        try {
            const response = await fetch(`${this.baseUrl}/chat/conversations`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load conversations');

            const data = await response.json();
            const conversations = data.data || [];
            const conversation = conversations.find(c => c.id === conversationId);

            if (conversation) {
                // Determine participant info
                const userId = conversation.user ? conversation.user.id : conversation.other_participant.id;
                const userName = conversation.user ? conversation.user.name : conversation.other_participant.name;
                const userAvatar = conversation.user ? (conversation.user.logo || '/images/default-user.png') : (conversation.other_participant.logo || '/images/default-user.png');
                
                await this.openChatBox(conversationId, userId, userName, userAvatar);
            } else {
                this.showError('Conversation not found');
            }
        } catch (error) {
            console.error('Error opening chat:', error);
            this.showError('Failed to open chat');
        }
    }

    async openChatBox(conversationId, userId, userName, userAvatar) {
        // Chat boxes work independently - don't auto-open main widget (Facebook style)
        
        if (this.activeConversations.has(conversationId)) {
            const chatBox = document.getElementById(`chatBox_${conversationId}`);
            if (chatBox) {
                chatBox.classList.remove('minimized');
                this.scrollToBottom(conversationId);
            }
            return;
        }

        // Don't hide conversations list - chat boxes appear separately (Facebook style)
        // Chat boxes container is now outside the main widget
        
        const chatBoxesContainer = document.getElementById('chatBoxesContainer');
        if (!chatBoxesContainer) {
            console.error('Chat boxes container not found');
            return;
        }

        const chatBox = this.createChatBox(conversationId, userId, userName, userAvatar);
        chatBoxesContainer.appendChild(chatBox);
        
        // Position new chat box to the right of existing ones
        this.updateChatBoxPositions();

        await this.loadMessages(conversationId);

        this.activeConversations.set(conversationId, {
            userId,
            userName,
            userAvatar
        });

        // Start polling for new messages
        this.startPolling(conversationId);

        const convItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (convItem) convItem.classList.add('active');
    }

    createChatBox(conversationId, userId, userName, userAvatar) {
        const chatBox = document.createElement('div');
        chatBox.className = 'chat-box';
        chatBox.id = `chatBox_${conversationId}`;
        chatBox.setAttribute('data-conversation-id', conversationId);

        const status = this.getUserStatus(userId, 'company') || 'offline';

        chatBox.innerHTML = `
            <div class="chat-box-header">
                <div class="chat-box-user-info">
                    <div class="chat-box-avatar">
                        <img src="${userAvatar}" alt="${this.escapeHtml(userName)}">
                        <span class="chat-status-indicator chat-status-${status}" 
                              data-user-id="${userId}" 
                              data-user-type="company"></span>
                    </div>
                    <div class="chat-box-user-details">
                        <div class="chat-box-user-name">${this.escapeHtml(userName)}</div>
                        <div class="chat-box-user-status">
                            <span class="status-text">${this.capitalize(status)}</span>
                        </div>
                    </div>
                </div>
                <div class="chat-box-actions">
                    <button class="chat-box-action-btn" onclick="chatWidget.minimizeChatBox(${conversationId})" title="${window.CHAT_TRANSLATIONS?.minimize || 'Minimize'}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button class="chat-box-action-btn" onclick="chatWidget.closeChatBox(${conversationId})" title="${window.CHAT_TRANSLATIONS?.close || 'Close'}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="chat-box-messages" id="chatMessages_${conversationId}">
                <div class="chat-messages-loading">
                    <i class="fas fa-spinner fa-spin"></i> ${window.CHAT_TRANSLATIONS?.loadingMessages || 'Loading messages...'}
                </div>
            </div>
            <div class="chat-typing-indicator" id="typingIndicator_${conversationId}" style="display: none;">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span class="typing-text">${this.escapeHtml(userName)} ${window.CHAT_TRANSLATIONS?.isTyping || 'is typing...'}</span>
            </div>
            <div class="chat-file-preview" id="filePreview_${conversationId}"></div>
            <div class="chat-box-input">
                <div class="chat-input-actions">
                    <button class="chat-input-btn" onclick="chatWidget.toggleFileUpload(${conversationId})" title="${window.CHAT_TRANSLATIONS?.attachFile || 'Attach File'}">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>
                <input type="text" 
                       class="chat-message-input" 
                       id="messageInput_${conversationId}"
                       placeholder="${window.CHAT_TRANSLATIONS?.typeMessage || 'Type a message...'}"
                       onkeypress="chatWidget.handleChatInput(event, ${conversationId})"
                       oninput="chatWidget.handleTyping(${conversationId})">
                <button class="chat-send-btn" onclick="chatWidget.sendMessage(${conversationId})" title="${window.CHAT_TRANSLATIONS?.send || 'Send'}">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <input type="file" 
                   id="fileInput_${conversationId}" 
                   class="chat-file-input" 
                   style="display: none;"
                   onchange="chatWidget.handleFileSelect(event, ${conversationId})"
                   multiple
                   accept="image/*,.pdf,.doc,.docx,.txt,.xls,.xlsx">
        `;

        return chatBox;
    }

    async loadMessages(conversationId) {
        try {
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load messages');

            const data = await response.json();
            const messages = data.data || [];
            
            // Store last message ID for polling
            if (messages.length > 0) {
                const lastMsg = messages[messages.length - 1];
                this.lastMessageIds.set(conversationId, lastMsg.id);
            }
            
            this.renderMessages(conversationId, messages);
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Failed to load messages');
        }
    }

    // Start polling for new messages
    startPolling(conversationId) {
        // Clear any existing polling for this conversation
        if (this.pollingIntervals.has(conversationId)) {
            clearInterval(this.pollingIntervals.get(conversationId));
        }

        // Poll every 2 seconds for new messages
        const interval = setInterval(async () => {
            await this.checkNewMessages(conversationId);
        }, 2000); // 2 seconds

        this.pollingIntervals.set(conversationId, interval);
    }

    // Stop polling for a conversation
    stopPolling(conversationId) {
        if (this.pollingIntervals.has(conversationId)) {
            clearInterval(this.pollingIntervals.get(conversationId));
            this.pollingIntervals.delete(conversationId);
        }
    }

    // Check for new messages
    async checkNewMessages(conversationId) {
        try {
            const lastMessageId = this.lastMessageIds.get(conversationId) || 0;
            
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages/new?since=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const data = await response.json();
            const newMessages = data.data || [];

            if (newMessages.length > 0) {
                // Check if any messages are incoming (from other users)
                const user = window.currentUser || { id: null, type: null };
                const company = window.currentCompany || { id: null };
                let hasIncomingMessage = false;
                
                // Add new messages to chat
                newMessages.forEach(msg => {
                    // Check if message is incoming (not from current user)
                    const isIncoming = (user.id && msg.sender_type === 'user' && msg.sender_id !== user.id) ||
                                      (company.id && msg.sender_type === 'company' && msg.sender_id !== company.id) ||
                                      (user.id && msg.sender_type === 'company') ||
                                      (company.id && msg.sender_type === 'user');
                    
                    if (isIncoming) {
                        hasIncomingMessage = true;
                    }
                    
                    this.addMessageToChat(conversationId, msg, true); // Skip sound - we'll play it once after all messages
                    // Update last message ID
                    this.lastMessageIds.set(conversationId, msg.id);
                });
                
                // Play sound only for incoming messages
                if (hasIncomingMessage) {
                    this.playNotificationSound();
                }

                // Update unread count
                this.updateUnreadCount();
            }
        } catch (error) {
            console.error('Error checking new messages:', error);
        }
    }

    renderMessages(conversationId, messages) {
        const container = document.getElementById(`chatMessages_${conversationId}`);
        if (!container) return;

        container.innerHTML = messages.map(msg => {
            const isOwn = msg.is_own;
            let attachmentsHtml = '';
            
            if (msg.attachments && msg.attachments.length > 0) {
                attachmentsHtml = msg.attachments.map(att => {
                    if (att.file_type === 'image') {
                        return `
                            <div class="chat-attachment chat-attachment-image">
                                <img src="${att.thumbnail_url || att.file_url}" 
                                     alt="${this.escapeHtml(att.file_name)}"
                                     onclick="chatWidget.openImageModal('${att.file_url}')"
                                     class="chat-attachment-image">
                            </div>
                        `;
                    } else {
                        return `
                            <div class="chat-attachment chat-attachment-file">
                                <a href="${att.file_url}" download="${this.escapeHtml(att.file_name)}" class="chat-attachment-file">
                                    <i class="fas fa-file ${this.getFileIcon(att.file_type)} chat-attachment-icon"></i>
                                    <div class="chat-attachment-info">
                                        <div class="chat-attachment-name">${this.escapeHtml(att.file_name)}</div>
                                        <div class="chat-attachment-size">${att.formatted_size}</div>
                                    </div>
                                </a>
                            </div>
                        `;
                    }
                }).join('');
            }

            return `
                <div class="chat-message ${isOwn ? 'own' : 'other'}">
                    <div class="chat-message-content">
                        ${msg.message ? `<div>${this.escapeHtml(msg.message)}</div>` : ''}
                        ${attachmentsHtml}
                        <div class="chat-message-time">${this.formatTime(msg.created_at)}</div>
                    </div>
                </div>
            `;
        }).join('');

        this.scrollToBottom(conversationId);
    }

    async sendMessage(conversationId) {
        const input = document.getElementById(`messageInput_${conversationId}`);
        const message = input ? input.value.trim() : '';
        const files = this.pendingFiles.get(conversationId) || [];

        if (!message && files.length === 0) return;

        if (input) input.disabled = true;
        const sendBtn = input ? input.nextElementSibling : null;
        if (sendBtn) sendBtn.disabled = true;

        try {
            const formData = new FormData();
            if (message) {
                formData.append('message', message);
            }

            files.forEach((file) => {
                formData.append('file', file);
            });

            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to send message');
            }

            const result = await response.json();
            
            // Add message immediately to chat
            if (result.success && result.data && result.data.id) {
                this.addMessageToChat(conversationId, result.data);
                this.lastMessageIds.set(conversationId, result.data.id);
            } else {
                console.error('Invalid response format:', result);
                throw new Error('Invalid response from server');
            }

            if (input) input.value = '';
            this.clearFilePreview(conversationId);
            this.pendingFiles.delete(conversationId);

            if (input) input.disabled = false;
            if (sendBtn) sendBtn.disabled = false;

            this.sendTypingIndicator(conversationId, false);
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError(error.message || 'Failed to send message');
            if (input) input.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
        }
    }

    handleFileSelect(event, conversationId) {
        const files = Array.from(event.target.files);
        const validFiles = [];
        const maxSize = 10 * 1024 * 1024;

        files.forEach(file => {
            if (file.size > maxSize) {
                this.showError(`File "${file.name}" exceeds 10MB limit`);
                return;
            }

            const allowedTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            if (!allowedTypes.includes(file.type)) {
                this.showError(`File type "${file.type}" is not allowed`);
                return;
            }

            validFiles.push(file);
        });

        if (validFiles.length > 0) {
            this.pendingFiles.set(conversationId, validFiles);
            this.renderFilePreview(conversationId, validFiles);
        }

        event.target.value = '';
    }

    renderFilePreview(conversationId, files) {
        const previewContainer = document.getElementById(`filePreview_${conversationId}`);
        if (!previewContainer) return;

        previewContainer.innerHTML = files.map((file, index) => {
            const isImage = file.type.startsWith('image/');
            const fileSize = this.formatFileSize(file.size);
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = previewContainer.querySelector(`[data-file-index="${index}"] img`);
                    if (img) img.src = e.target.result;
                };
                reader.readAsDataURL(file);

                return `
                    <div class="chat-file-preview-item" data-file-index="${index}">
                        <img src="" alt="${this.escapeHtml(file.name)}" class="chat-file-preview-image">
                        <div class="chat-file-preview-info">
                            <div class="chat-file-preview-name">${this.escapeHtml(file.name)}</div>
                            <div class="chat-file-preview-size">${fileSize}</div>
                        </div>
                        <button class="chat-file-preview-remove" onclick="chatWidget.removeFile(${conversationId}, ${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            } else {
                return `
                    <div class="chat-file-preview-item" data-file-index="${index}">
                        <i class="fas fa-file ${this.getFileIcon(file.type)} chat-file-preview-icon"></i>
                        <div class="chat-file-preview-info">
                            <div class="chat-file-preview-name">${this.escapeHtml(file.name)}</div>
                            <div class="chat-file-preview-size">${fileSize}</div>
                        </div>
                        <button class="chat-file-preview-remove" onclick="chatWidget.removeFile(${conversationId}, ${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
        }).join('');

        previewContainer.classList.add('active');
    }

    removeFile(conversationId, index) {
        const files = this.pendingFiles.get(conversationId) || [];
        files.splice(index, 1);
        
        if (files.length === 0) {
            this.pendingFiles.delete(conversationId);
            this.clearFilePreview(conversationId);
        } else {
            this.pendingFiles.set(conversationId, files);
            this.renderFilePreview(conversationId, files);
        }
    }

    clearFilePreview(conversationId) {
        const previewContainer = document.getElementById(`filePreview_${conversationId}`);
        if (previewContainer) {
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('active');
        }
    }

    toggleFileUpload(conversationId) {
        const fileInput = document.getElementById(`fileInput_${conversationId}`);
        if (fileInput) {
            fileInput.click();
        }
    }

    handleChatInput(event, conversationId) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            this.sendMessage(conversationId);
        }
    }

    handleTyping(conversationId) {
        // Typing indicator (optional - can be removed if not needed)
        this.sendTypingIndicator(conversationId, true);
        
        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            this.sendTypingIndicator(conversationId, false);
        }, 3000);
    }

    async sendTypingIndicator(conversationId, isTyping) {
        // Optional: Store typing status in database
        // For now, we'll skip this to keep it simple
    }

    addMessageToChat(conversationId, messageData, skipSound = false) {
        const container = document.getElementById(`chatMessages_${conversationId}`);
        if (!container) return;

        const user = window.currentUser || { id: null, type: null };
        const company = window.currentCompany || { id: null };
        
        // Determine if message is from current user
        const isOwn = (user.id && messageData.sender_type === 'user' && messageData.sender_id === user.id) ||
                     (company.id && messageData.sender_type === 'company' && messageData.sender_id === company.id);
        let attachmentsHtml = '';

        if (messageData.attachments && messageData.attachments.length > 0) {
            attachmentsHtml = messageData.attachments.map(att => {
                if (att.file_type === 'image') {
                    return `
                        <div class="chat-attachment chat-attachment-image">
                            <img src="${att.thumbnail_url || att.file_url}" 
                                 alt="${this.escapeHtml(att.file_name)}"
                                 onclick="chatWidget.openImageModal('${att.file_url}')"
                                 class="chat-attachment-image">
                        </div>
                    `;
                } else {
                    return `
                        <div class="chat-attachment chat-attachment-file">
                            <a href="${att.file_url}" download="${this.escapeHtml(att.file_name)}" class="chat-attachment-file">
                                <i class="fas fa-file ${this.getFileIcon(att.file_type)} chat-attachment-icon"></i>
                                <div class="chat-attachment-info">
                                    <div class="chat-attachment-name">${this.escapeHtml(att.file_name)}</div>
                                    <div class="chat-attachment-size">${att.formatted_size}</div>
                                </div>
                            </a>
                        </div>
                    `;
                }
            }).join('');
        }

        const messageEl = document.createElement('div');
        messageEl.className = `chat-message ${isOwn ? 'own' : 'other'}`;
        messageEl.innerHTML = `
            <div class="chat-message-content">
                ${messageData.message ? `<div>${this.escapeHtml(messageData.message)}</div>` : ''}
                ${attachmentsHtml}
                <div class="chat-message-time">${this.formatTime(messageData.created_at)}</div>
            </div>
        `;

        container.appendChild(messageEl);
        this.scrollToBottom(conversationId);
        
        // Only play sound if not skipped and message is incoming
        if (!skipSound && !isOwn) {
            this.playNotificationSound();
        }
    }

    openImageModal(imageUrl) {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        `;
        modal.innerHTML = `
            <img src="${imageUrl}" style="max-width: 90%; max-height: 90%; object-fit: contain;">
        `;
        modal.onclick = () => modal.remove();
        document.body.appendChild(modal);
    }

    toggleWidget() {
        const widget = document.getElementById('instantChatWidget');
        if (!widget) return;

        this.isOpen = !this.isOpen;
        widget.classList.toggle('active', this.isOpen);

        if (this.isOpen) {
            this.loadConversations();
            this.updateActivity();
        }
        
        // Update chat box positions when main widget toggles
        setTimeout(() => this.updateChatBoxPositions(), 100);
    }

    minimizeWidget() {
        const widget = document.getElementById('instantChatWidget');
        if (!widget) return;

        this.isMinimized = !this.isMinimized;
        widget.classList.toggle('minimized', this.isMinimized);
    }

    closeWidget() {
        this.isOpen = false;
        const widget = document.getElementById('instantChatWidget');
        if (widget) widget.classList.remove('active');
        
        // Stop all polling
        this.pollingIntervals.forEach((interval, convId) => {
            clearInterval(interval);
        });
        this.pollingIntervals.clear();
    }

    updateChatBoxPositions() {
        // Calculate position for chat boxes container to avoid overlap
        const chatBoxesContainer = document.getElementById('chatBoxesContainer');
        const mainWidget = document.getElementById('instantChatWidget');
        
        if (!chatBoxesContainer) return;
        
        const chatBoxes = chatBoxesContainer.querySelectorAll('.chat-box');
        const mainWidgetWidth = 360; // Main widget width
        const mainWidgetRight = 20; // Main widget right position
        const gap = 10; // Gap between main widget and chat boxes
        
        // Check if main widget is open/visible
        const isMainWidgetOpen = mainWidget && mainWidget.classList.contains('active');
        
        // Position chat boxes container to the left of main widget (if open) or at edge
        let chatBoxesRight;
        if (isMainWidgetOpen) {
            chatBoxesRight = mainWidgetWidth + mainWidgetRight + gap;
        } else {
            chatBoxesRight = 20; // Position at edge if main widget is closed
        }
        
        chatBoxesContainer.style.right = chatBoxesRight + 'px';
        
        // Ensure chat boxes don't overflow viewport
        const maxWidth = window.innerWidth - chatBoxesRight - 20; // 20px left margin
        if (chatBoxes.length > 0) {
            const totalWidth = Array.from(chatBoxes).reduce((sum, box) => {
                const boxWidth = box.classList.contains('minimized') ? 250 : 320;
                return sum + boxWidth + gap;
            }, 0);
            
            if (totalWidth > maxWidth) {
                // If too many boxes, allow horizontal scrolling
                chatBoxesContainer.style.overflowX = 'auto';
            } else {
                chatBoxesContainer.style.overflowX = 'visible';
            }
        }
    }

    minimizeChatBox(conversationId) {
        const chatBox = document.getElementById(`chatBox_${conversationId}`);
        if (chatBox) {
            chatBox.classList.toggle('minimized');
        }
    }

    closeChatBox(conversationId) {
        const chatBox = document.getElementById(`chatBox_${conversationId}`);
        if (chatBox) chatBox.remove();

        this.activeConversations.delete(conversationId);
        this.stopPolling(conversationId);
        this.lastMessageIds.delete(conversationId);

        // Update positions after closing
        this.updateChatBoxPositions();

        const convItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (convItem) convItem.classList.remove('active');
    }

    restoreBadgeState() {
        // Restore badge state from localStorage
        try {
            const storedCount = localStorage.getItem('chatUnreadCount');
            const storedBadgeVisible = localStorage.getItem('chatBadgeVisible');
            
            if (storedCount !== null && storedBadgeVisible === 'true') {
                const badge = document.getElementById('chatUnreadBadge');
                if (badge) {
                    const count = parseInt(storedCount) || 0;
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'flex';
                        badge.style.visibility = 'visible';
                        badge.style.opacity = '1';
                        this.lastBadgeCount = count;
                        this.unreadCount = count;
                        console.log('Badge state restored from localStorage - count:', count);
                    }
                }
            }
        } catch (error) {
            console.error('Error restoring badge state:', error);
        }
    }
    
    saveBadgeState(count, visible) {
        // Save badge state to localStorage
        try {
            localStorage.setItem('chatUnreadCount', count.toString());
            localStorage.setItem('chatBadgeVisible', visible ? 'true' : 'false');
        } catch (error) {
            console.error('Error saving badge state:', error);
        }
    }
    
    clearBadgeState() {
        // Clear badge state from localStorage (when messages are read)
        try {
            localStorage.removeItem('chatUnreadCount');
            localStorage.removeItem('chatBadgeVisible');
        } catch (error) {
            console.error('Error clearing badge state:', error);
        }
    }

    async updateUnreadCount() {
        try {
            const response = await fetch(this.baseUrl + '/chat/conversations', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                console.log('Failed to fetch conversations for unread count:', response.status);
                return;
            }

            const data = await response.json();
            const conversations = data.data || [];
            const newUnreadCount = conversations.reduce((sum, conv) => sum + (conv.unread_count || 0), 0);
            
            console.log('Fetched unread count from server:', newUnreadCount, 'Current local count:', this.unreadCount);
            
            // Always use the server's unread count (source of truth)
            // Only update badge if count actually changed to avoid unnecessary DOM updates
            const countChanged = newUnreadCount !== this.unreadCount;
            
            if (countChanged) {
                console.log('Unread count changed from', this.unreadCount, 'to', newUnreadCount);
                this.unreadCount = newUnreadCount;
            }

            // Always update the badge display based on server's unread count
            // This ensures badge persists until messages are actually read
            const badge = document.getElementById('chatUnreadBadge');
            const countEl = document.getElementById('chatUnreadCount');

            if (badge) {
                // Always show badge if there are unread messages
                if (newUnreadCount > 0) {
                    // Clear any pending hide timeout and reset confirmation counter
                    if (this.badgeHideTimeout) {
                        clearTimeout(this.badgeHideTimeout);
                        this.badgeHideTimeout = null;
                    }
                    this.zeroCountConfirmations = 0;
                    
                    badge.textContent = newUnreadCount > 99 ? '99+' : newUnreadCount;
                    badge.style.display = 'flex';
                    badge.style.visibility = 'visible';
                    badge.style.opacity = '1';
                    this.lastBadgeCount = newUnreadCount;
                    
                    // Save badge state to localStorage
                    this.saveBadgeState(newUnreadCount, true);
                    
                    console.log('Badge updated - showing with count:', badge.textContent, 'Server count:', newUnreadCount);
                } else {
                    // Server reports 0 unread messages
                    // Check if badge was previously showing (from localStorage or last update)
                    const storedCount = localStorage.getItem('chatUnreadCount');
                    const storedVisible = localStorage.getItem('chatBadgeVisible');
                    
                    if (this.lastBadgeCount > 0 || (storedCount && parseInt(storedCount) > 0 && storedVisible === 'true')) {
                        // Badge was showing - keep it visible even if server says 0
                        // This prevents badge from disappearing when switching pages
                        // Only hide when messages are actually read (markAsRead clears localStorage)
                        const displayCount = this.lastBadgeCount > 0 ? this.lastBadgeCount : parseInt(storedCount) || 0;
                        badge.textContent = displayCount > 99 ? '99+' : displayCount;
                        badge.style.display = 'flex';
                        badge.style.visibility = 'visible';
                        badge.style.opacity = '1';
                        console.log('Server reports 0, but keeping badge visible (count:', displayCount, ') until messages are read');
                    } else {
                        // Badge was already hidden, keep it hidden
                        if (badge.style.display !== 'none') {
                            badge.style.display = 'none';
                            badge.style.visibility = 'hidden';
                        }
                        this.clearBadgeState();
                    }
                }
            } else {
                console.warn('Badge element not found! ID: chatUnreadBadge');
            }

            if (countEl) {
                countEl.textContent = newUnreadCount > 99 ? '99+' : newUnreadCount;
                if (countChanged) {
                    console.log('Count element updated:', countEl.textContent);
                }
            }
            
            // Update local state to match server
            this.unreadCount = newUnreadCount;
        } catch (error) {
            console.error('Error updating unread count:', error);
        }
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

    getUserStatus(userId, userType) {
        return 'online';
    }

    scrollToBottom(conversationId) {
        const container = document.getElementById(`chatMessages_${conversationId}`);
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }

    formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;

        if (diff < 60000) return window.CHAT_TRANSLATIONS?.justNow || 'Just now';
        if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
        if (diff < 86400000) return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    formatFileSize(bytes) {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + ' GB';
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    }

    getFileIcon(fileType) {
        if (fileType.includes('pdf')) return 'fa-file-pdf';
        if (fileType.includes('word') || fileType.includes('document')) return 'fa-file-word';
        if (fileType.includes('excel') || fileType.includes('spreadsheet')) return 'fa-file-excel';
        if (fileType.includes('image')) return 'fa-file-image';
        return 'fa-file';
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getAuthToken() {
        // Not needed for web routes - using session auth
        return '';
    }

    playNotificationSound() {
        try {
            // Use base URL if available
            const baseUrl = this.baseUrl || '';
            const soundPath = baseUrl + '/sounds/notification.mp3';
            
            // Try to play a sound file if it exists
            const audio = new Audio(soundPath);
            audio.volume = 0.7; // Increase volume slightly
            audio.preload = 'auto';
            
            // Handle play promise with better error handling
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        // Sound played successfully
                        console.log('Notification sound played');
                    })
                    .catch(error => {
                        // If sound file doesn't exist or autoplay blocked, generate a simple beep sound
                        console.log('Audio file play failed, using beep sound:', error);
                        this.playBeepSound();
                    });
            }
        } catch (e) {
            // Fallback to beep sound if audio file fails
            console.log('Audio creation failed, using beep sound:', e);
            this.playBeepSound();
        }
    }
    
    playBeepSound() {
        try {
            // Create or resume audio context (browsers require user interaction first)
            let audioContext = this.audioContext;
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                this.audioContext = audioContext;
            }
            
            // Resume audio context if suspended (required after user interaction)
            if (audioContext.state === 'suspended') {
                audioContext.resume().then(() => {
                    this.playBeepSoundInternal(audioContext);
                }).catch(() => {
                    console.log('Could not resume audio context');
                });
            } else {
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

    searchConversations(query) {
        const items = document.querySelectorAll('.chat-conversation-item');
        items.forEach(item => {
            const name = item.querySelector('.chat-conversation-name')?.textContent.toLowerCase() || '';
            item.style.display = name.includes(query.toLowerCase()) ? 'flex' : 'none';
        });
    }

    startStatusPolling() {
        // Poll every 30 seconds for status updates
        // This ensures unread count stays accurate
        setInterval(() => {
            this.updateUnreadCount();
            this.updateActivity();
        }, 30000);
        
        // Also update immediately on initialization
        setTimeout(() => {
            this.updateUnreadCount();
        }, 1000);
    }
    
    startGlobalPolling() {
        // Poll all conversations for new messages, reactions, and replies
        // This runs continuously even when no conversation is open
        // Check every 3 seconds for new messages in all conversations
        console.log('Starting global polling for notifications...');
        this.globalPollingInterval = setInterval(async () => {
            await this.checkAllConversationsForNewMessages();
        }, 3000);
        
        // Also check immediately after a short delay to initialize lastMessageIds
        setTimeout(async () => {
            await this.checkAllConversationsForNewMessages();
        }, 2000);
    }
    
    async checkAllConversationsForNewMessages() {
        // Check all conversations for new messages to play sound notifications
        // This runs on all pages, not just the chat page
        console.log('Checking all conversations for new messages...', this.allConversations?.length || 0, 'conversations');
        if (!this.allConversations || this.allConversations.length === 0) {
            // Reload conversations if we don't have them
            try {
                const response = await fetch(this.baseUrl + '/chat/conversations', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    const conversations = data.data || [];
                    this.allConversations = conversations;
                    
                    // Initialize lastMessageId for conversations that have messages
                    conversations.forEach(conv => {
                        if (conv.last_message && !this.lastMessageIds.has(conv.id)) {
                            this.lastMessageIds.set(conv.id, 0);
                        }
                    });
                }
            } catch (error) {
                return; // Silently fail
            }
        }
        
        // Check each conversation for new messages and reactions
        for (const conv of this.allConversations) {
            // Only check conversations that have messages
            if (conv.last_message) {
                console.log('Checking conversation', conv.id, 'for new messages');
                await this.checkNewMessagesForNotification(conv.id);
                // Also check for reaction changes (less frequently - every 10 seconds)
                // We'll check reactions every other call to avoid too many API calls
                if (Math.random() < 0.3) { // 30% chance each call = ~every 10 seconds
                    await this.checkReactionChangesForNotification(conv.id);
                }
            }
        }
    }
    
    async checkNewMessagesForNotification(conversationId) {
        // Check for new messages in a conversation (for sound notifications)
        // This is separate from the active conversation polling
        // This runs on all pages, not just when conversation is open
        try {
            let lastMessageId = this.lastMessageIds.get(conversationId);
            
            // If we don't have a lastMessageId or it's 0, initialize it from the conversation's last message
            if (!lastMessageId || lastMessageId === 0) {
                // Get the last message ID by fetching recent messages
                try {
                    const msgResponse = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages?limit=1`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (msgResponse.ok) {
                        const msgData = await msgResponse.json();
                        if (msgData.data && msgData.data.length > 0) {
                            lastMessageId = msgData.data[msgData.data.length - 1].id;
                            this.lastMessageIds.set(conversationId, lastMessageId);
                            // Set sinceId to the last message ID so we only check for NEW messages after this
                            // This prevents playing sound for existing messages on first load
                            const sinceId = lastMessageId;
                            
                            // Now check for new messages after this last message
                            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages/new?since=${sinceId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                },
                                credentials: 'same-origin'
                            });

                            if (response.ok) {
                                const data = await response.json();
                                const newMessages = data.data || [];
                                
                                if (newMessages.length > 0) {
                                    // Check if any messages are incoming (from other users)
                                    const user = window.currentUser || { id: null, type: null };
                                    const company = window.currentCompany || { id: null };
                                    let hasIncomingMessage = false;
                                    let highestMessageId = lastMessageId; // Track highest message ID
                                    
                                    newMessages.forEach(msg => {
                                        // Check if message is incoming (not from current user)
                                        let isIncoming = false;
                                        
                                        // If current user is a user
                                        if (user.id) {
                                            // Message is incoming if it's from a company OR from another user
                                            if (msg.sender_type === 'company') {
                                                isIncoming = true;
                                            } else if (msg.sender_type === 'user' && msg.sender_id !== user.id) {
                                                isIncoming = true;
                                            }
                                        }
                                        // If current user is a company
                                        else if (company.id) {
                                            // Message is incoming if it's from a user OR from another company
                                            if (msg.sender_type === 'user') {
                                                isIncoming = true;
                                            } else if (msg.sender_type === 'company' && msg.sender_id !== company.id) {
                                                isIncoming = true;
                                            }
                                        }
                                        
                                        if (isIncoming) {
                                            hasIncomingMessage = true;
                                        }
                                        
                                        // Track highest message ID
                                        if (msg.id > highestMessageId) {
                                            highestMessageId = msg.id;
                                        }
                                    });
                                    
                                    // Update last message ID to the highest ID (only once after processing all messages)
                                    this.lastMessageIds.set(conversationId, highestMessageId);
                                    
                                    // Play sound only ONCE if there are any incoming messages (not for each message)
                                    if (hasIncomingMessage) {
                                        console.log('New incoming message(s) detected in conversation', conversationId, '- playing sound once and updating unread count');
                                        this.playNotificationSound();
                                        // Update unread count immediately
                                        await this.updateUnreadCount();
                                    } else {
                                        console.log('New messages found during initialization but none are incoming (from current user)');
                                    }
                                }
                            }
                            return; // Done with initialization
                        } else {
                            // No messages yet, set to 0
                            this.lastMessageIds.set(conversationId, 0);
                            return;
                        }
                    }
                } catch (e) {
                    // If we can't get last message, skip this conversation for now
                    console.log('Error initializing lastMessageId for conversation', conversationId, e);
                    return;
                }
            }
            
            const sinceId = lastMessageId;
            
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages/new?since=${sinceId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const data = await response.json();
            const newMessages = data.data || [];

            if (newMessages.length > 0) {
                console.log('Found', newMessages.length, 'new messages in conversation', conversationId);
                // Check if any messages are incoming (from other users)
                const user = window.currentUser || { id: null, type: null };
                const company = window.currentCompany || { id: null };
                let hasIncomingMessage = false;
                
                let highestMessageId = lastMessageId; // Track highest message ID
                
                newMessages.forEach(msg => {
                    // Check if message is incoming (not from current user)
                    // This includes regular messages, replies, and reactions
                    let isIncoming = false;
                    
                    // If current user is a user
                    if (user.id) {
                        // Message is incoming if it's from a company OR from another user
                        if (msg.sender_type === 'company') {
                            isIncoming = true;
                        } else if (msg.sender_type === 'user' && msg.sender_id !== user.id) {
                            isIncoming = true;
                        }
                    }
                    // If current user is a company
                    else if (company.id) {
                        // Message is incoming if it's from a user OR from another company
                        if (msg.sender_type === 'user') {
                            isIncoming = true;
                        } else if (msg.sender_type === 'company' && msg.sender_id !== company.id) {
                            isIncoming = true;
                        }
                    }
                    
                    if (isIncoming) {
                        hasIncomingMessage = true;
                    }
                    
                    // Track highest message ID
                    if (msg.id > highestMessageId) {
                        highestMessageId = msg.id;
                    }
                });
                
                // Update last message ID to the highest ID (only once after processing all messages)
                this.lastMessageIds.set(conversationId, highestMessageId);
                
                // Play sound only ONCE if there are any incoming messages (not for each message)
                if (hasIncomingMessage) {
                    console.log('New incoming message(s) detected in conversation', conversationId, '- playing sound once and updating unread count');
                    this.playNotificationSound();
                    // Update unread count immediately
                    await this.updateUnreadCount();
                } else {
                    console.log('New messages found but none are incoming (from current user)');
                    // Still update unread count even if no incoming messages (in case count changed)
                    await this.updateUnreadCount();
                }
            } else {
                console.log('No new messages in conversation', conversationId, 'since', sinceId);
            }
        } catch (error) {
            // Silently fail - this is background polling
            console.log('Error checking messages for notification:', error);
        }
    }
    
    async checkReactionChangesForNotification(conversationId) {
        // Check for reaction changes in a conversation (for sound notifications)
        // This runs on all pages, not just when conversation is open
        try {
            // Get recent messages to check for reaction changes
            const response = await fetch(`${this.baseUrl}/chat/conversations/${conversationId}/messages?limit=20`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const data = await response.json();
            const messages = data.data || [];
            
            // Store current reactions for comparison
            const currentReactionsMap = new Map();
            messages.forEach(msg => {
                if (msg.reactions && msg.reactions.length > 0) {
                    currentReactionsMap.set(msg.id, msg.reactions);
                }
            });
            
            // Get stored reactions (if we have them)
            const storedReactionsMap = this.storedReactionsMap || new Map();
            if (!this.storedReactionsMap) {
                this.storedReactionsMap = new Map();
            }
            
            // Check for new reactions
            const user = window.currentUser || { id: null, type: null };
            const company = window.currentCompany || { id: null };
            let hasNewReaction = false;
            
            messages.forEach(msg => {
                if (msg.reactions && msg.reactions.length > 0) {
                    const currentReactions = currentReactionsMap.get(msg.id) || [];
                    const storedReactions = storedReactionsMap.get(msg.id) || [];
                    
                    // Check if any reaction count increased
                    currentReactions.forEach(newReaction => {
                        const oldReaction = storedReactions.find(r => r.emoji === newReaction.emoji);
                        const oldCount = oldReaction ? oldReaction.count : 0;
                        const newCount = newReaction.count || 0;
                        
                        if (newCount > oldCount) {
                            // Check if it's from someone else
                            const isCurrentUserReaction = newReaction.users && newReaction.users.some(u => 
                                (user.id && u.id === user.id && u.type === 'user') ||
                                (company.id && u.id === company.id && u.type === 'company')
                            );
                            
                            if (!isCurrentUserReaction) {
                                hasNewReaction = true;
                            }
                        }
                    });
                }
            });
            
            // Update stored reactions
            this.storedReactionsMap = currentReactionsMap;
            
            // Play sound if new reaction detected
            if (hasNewReaction) {
                console.log('New reaction detected in conversation', conversationId, '- playing sound');
                this.playNotificationSound();
                // Update unread count to refresh badge (in case there are new messages too)
                await this.updateUnreadCount();
            }
        } catch (error) {
            // Silently fail - this is background polling
            console.log('Error checking reactions for notification:', error);
        }
    }

    showError(message) {
        console.error(message);
        alert(message);
    }
    
    toggleHeaderDropdown() {
        const dropdown = document.getElementById('chatHeaderDropdown');
        if (!dropdown) return;
        
        if (dropdown.classList.contains('show')) {
            this.closeHeaderDropdown();
        } else {
            this.openHeaderDropdown();
        }
    }
    
    openHeaderDropdown() {
        const dropdown = document.getElementById('chatHeaderDropdown');
        if (!dropdown) return;
        
        dropdown.classList.add('show');
        this.loadHeaderDropdownConversations();
    }
    
    closeHeaderDropdown() {
        const dropdown = document.getElementById('chatHeaderDropdown');
        if (!dropdown) return;
        
        dropdown.classList.remove('show');
    }
    
    async loadHeaderDropdownConversations() {
        const content = document.getElementById('chatDropdownContent');
        if (!content) return;
        
        try {
            content.innerHTML = '<div class="chat-dropdown-loading">' + (window.CHAT_TRANSLATIONS?.loadingConversations || 'Loading conversations...') + '</div>';
            
            const response = await fetch(this.baseUrl + '/chat/conversations?filter=unread', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                content.innerHTML = '<div class="chat-dropdown-empty">Failed to load conversations</div>';
                return;
            }

            const data = await response.json();
            const conversations = data.data || [];
            
            // If no unread conversations, show recent conversations instead
            if (conversations.length === 0) {
                const allResponse = await fetch(this.baseUrl + '/chat/conversations?filter=all', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (allResponse.ok) {
                    const allData = await allResponse.json();
                    const allConversations = allData.data || [];
                    // Show only recent conversations (last 5)
                    const recentConversations = allConversations.slice(0, 5);
                    this.renderHeaderDropdownConversations(recentConversations, content);
                } else {
                    content.innerHTML = '<div class="chat-dropdown-empty">No conversations yet</div>';
                }
            } else {
                // Show unread conversations (max 10)
                const unreadConversations = conversations.slice(0, 10);
                this.renderHeaderDropdownConversations(unreadConversations, content);
            }
        } catch (error) {
            console.error('Error loading header dropdown conversations:', error);
            content.innerHTML = '<div class="chat-dropdown-empty">Failed to load conversations</div>';
        }
    }
    
    renderHeaderDropdownConversations(conversations, container) {
        if (conversations.length === 0) {
            container.innerHTML = '<div class="chat-dropdown-empty">No conversations yet</div>';
            return;
        }
        
        const baseUrl = this.baseUrl || window.CHAT_BASE_URL || '';
        const chatUrl = baseUrl + '/chat';
        
        container.innerHTML = conversations.map(conv => {
            const lastMsg = conv.last_message;
            let preview = 'No messages yet';
            
            if (lastMsg) {
                if (lastMsg.message_type === 'image') {
                    preview = '📷 Image';
                } else if (lastMsg.message_type === 'file') {
                    preview = '📎 File';
                } else if (lastMsg.message) {
                    preview = lastMsg.message;
                    // Truncate long messages
                    if (preview.length > 50) {
                        preview = preview.substring(0, 50) + '...';
                    }
                }
            }
            
            // Use other_participant which is always available
            const participant = conv.other_participant || conv.company || conv.user;
            if (!participant) return ''; // Skip if no participant
            
            const participantName = participant.name || 'Unknown';
            const participantLogo = participant.logo || '/images/default-user.png';
            
            // Format time
            const time = this.formatTime(conv.last_message_at);
            
            return `
                <a href="${chatUrl}?conversation=${conv.id}" class="chat-dropdown-item" data-conversation-id="${conv.id}">
                    <div class="chat-dropdown-avatar">
                        <img src="${participantLogo}" alt="${this.escapeHtml(participantName)}" onerror="this.src='/images/default-user.png'">
                    </div>
                    <div class="chat-dropdown-info">
                        <div class="chat-dropdown-name">${this.escapeHtml(participantName)}</div>
                        <div class="chat-dropdown-preview">${this.escapeHtml(preview)}</div>
                    </div>
                    <div class="chat-dropdown-meta">
                        ${conv.unread_count > 0 ? `<div class="chat-dropdown-unread">${conv.unread_count > 99 ? '99+' : conv.unread_count}</div>` : ''}
                        <div class="chat-dropdown-time">${time}</div>
                    </div>
                </a>
            `;
        }).join('');
    }
}

let chatWidget;
document.addEventListener('DOMContentLoaded', () => {
    // Initialize when user is authenticated: header badge exists (all pages) or full widget exists (chat widget page)
    if (document.getElementById('chatUnreadBadge') || document.getElementById('instantChatWidget')) {
        chatWidget = new ChatWidget();
        window.chatWidget = chatWidget;
    }
});

})();

