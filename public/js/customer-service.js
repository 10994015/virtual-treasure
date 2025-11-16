// AI Customer Service Chat System
class CustomerServiceChat {
    constructor() {
        this.apiUrl = 'http://localhost:3001/api';
        this.token = localStorage.getItem('token');
        this.isMinimized = false;
        this.isTyping = false;
        this.messageHistory = [];

        this.init();
    }

    init() {
        this.setupElements();
        this.setupEventListeners();
        this.loadMessageHistory();
    }

    setupElements() {
        this.widget = document.getElementById('chatWidget');
        this.toggle = document.getElementById('chatToggle');
        this.messages = document.getElementById('chatMessages');
        this.input = document.getElementById('chatInput');
        this.sendBtn = document.getElementById('sendMessage');
        this.minimizeBtn = document.getElementById('minimizeChat');
        this.closeBtn = document.getElementById('closeChat');
        this.clearBtn = document.getElementById('clearChat');
        this.feedbackBtn = document.getElementById('feedbackBtn');
        this.notification = document.getElementById('chatNotification');
    }

    setupEventListeners() {
        // Toggle chat widget
        this.toggle.addEventListener('click', () => this.toggleChat());

        // Minimize/maximize
        this.minimizeBtn.addEventListener('click', () => this.toggleMinimize());

        // Close chat
        this.closeBtn.addEventListener('click', () => this.closeChat());

        // Send message
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize input
        this.input.addEventListener('input', () => this.autoResizeInput());

        // Clear chat
        this.clearBtn.addEventListener('click', () => this.clearChat());

        // Feedback
        this.feedbackBtn.addEventListener('click', () => this.showFeedback());

        // Quick actions
        this.setupQuickActions();
    }

    setupQuickActions() {
        // Use event delegation for dynamic content
        this.messages.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-action-btn') || e.target.closest('.quick-action-btn')) {
                const btn = e.target.classList.contains('quick-action-btn') ? e.target : e.target.closest('.quick-action-btn');
                const query = btn.getAttribute('data-query');
                if (query) {
                    this.sendQuickQuery(query);
                }
            }
        });

        // Initial quick actions
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const query = e.target.getAttribute('data-query') || e.target.closest('.quick-action-btn').getAttribute('data-query');
                if (query) {
                    this.sendQuickQuery(query);
                }
            });
        });
    }

    autoResizeInput() {
        this.input.style.height = 'auto';
        this.input.style.height = Math.min(this.input.scrollHeight, 80) + 'px';
    }

    toggleChat() {
        if (this.widget.classList.contains('show')) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    openChat() {
        this.widget.classList.add('show');
        this.widget.classList.remove('minimized');
        this.isMinimized = false;
        this.toggle.style.display = 'none';
        this.input.focus();

        // Hide notification when opening chat
        this.hideNotification();
    }

    closeChat() {
        this.widget.classList.remove('show');
        this.widget.classList.remove('minimized');
        this.toggle.style.display = 'flex';
    }

    toggleMinimize() {
        this.isMinimized = !this.isMinimized;
        if (this.isMinimized) {
            this.widget.classList.add('minimized');
        } else {
            this.widget.classList.remove('minimized');
        }
    }

    async sendMessage() {
        const message = this.input.value.trim();
        if (!message) return;

        // Disable input and send button
        this.input.disabled = true;
        this.sendBtn.disabled = true;
        this.sendBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

        // Add user message with sending status
        const userMessageDiv = this.addMessage(message, 'user', null, 'sending');
        this.input.value = '';

        try {
            // Show typing indicator
            this.showTypingIndicator();

            const response = await this.sendToAPI(message);

            // Update user message status
            this.updateMessageStatus(userMessageDiv, 'sent');

            this.hideTypingIndicator();
            this.addMessage(response.response, 'bot', response, 'delivered');

            // Show suggestions if available
            if (response.suggestedActions && response.suggestedActions.length > 0) {
                setTimeout(() => {
                    this.showSuggestions(response.suggestedActions);
                }, 500);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.updateMessageStatus(userMessageDiv, 'error');
            this.addMessage('抱歉，我現在無法處理您的請求。請稍後再試或聯絡客服支援。', 'bot', null, 'error');
            console.error('Chat API error:', error);
        } finally {
            // Re-enable input and send button
            this.input.disabled = false;
            this.sendBtn.disabled = false;
            this.sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            this.input.focus();
        }
    }

    sendQuickQuery(query) {
        this.input.value = query;
        this.sendMessage();
    }

    updateMessageStatus(messageDiv, status) {
        if (!messageDiv) return;

        const statusElement = messageDiv.querySelector('.message-status');
        if (statusElement) {
            statusElement.innerHTML = this.getStatusIcon(status);
        }
    }

    async sendToAPI(message) {
        const response = await fetch(`${this.apiUrl}/customer-service/chat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(this.token && { Authorization: `Bearer ${this.token}` })
            },
            body: JSON.stringify({
                message,
                context: {
                    userAgent: navigator.userAgent,
                    timestamp: new Date().toISOString(),
                    page: window.location.pathname
                }
            })
        });

        if (!response.ok) {
            throw new Error('API request failed');
        }

        const data = await response.json();
        return data.data.response;
    }

    addMessage(text, type, responseData = null, status = 'sent') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message`;

        const avatar = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';
        const statusIcon = this.getStatusIcon(status);

        messageDiv.innerHTML = `
            <div class="message-avatar">
                ${avatar}
            </div>
            <div class="message-content">
                <div class="message-text">${this.formatMessage(text)}</div>
                <div class="message-meta">
                    <span class="message-time">${this.formatTime(new Date())}</span>
                    <span class="message-status">${statusIcon}</span>
                </div>
                ${responseData && responseData.suggestedActions ? this.createActionButtons(responseData.suggestedActions) : ''}
            </div>
        `;

        this.messages.appendChild(messageDiv);
        this.scrollToBottom();

        // Save to history
        this.messageHistory.push({
            text,
            type,
            timestamp: new Date(),
            responseData,
            status
        });
        this.saveMessageHistory();

        return messageDiv;
    }

    getStatusIcon(status) {
        switch (status) {
            case 'sending':
                return '<i class="fas fa-circle-notch fa-spin" style="color: #ffa500;"></i>';
            case 'sent':
                return '<i class="fas fa-check" style="color: #28a745;"></i>';
            case 'delivered':
                return '<i class="fas fa-check-double" style="color: #28a745;"></i>';
            case 'error':
                return '<i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>';
            default:
                return '';
        }
    }

    formatMessage(text) {
        // Convert URLs to links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, '<a href="$1" target="_blank">$1</a>');
    }

    formatTime(date) {
        return date.toLocaleTimeString('zh-TW', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    createActionButtons(actions) {
        if (!actions || actions.length === 0) return '';

        const buttons = actions.map(action => {
            const actionText = this.getActionText(action);
            return `<button class="suggestion-btn" onclick="customerService.handleAction('${action}')">${actionText}</button>`;
        }).join('');

        return `<div class="message-suggestions">${buttons}</div>`;
    }

    getActionText(action) {
        const actionMap = {
            'view_profile': '查看個人資料',
            'update_security': '更新安全設定',
            'verify_email': '驗證郵箱',
            'browse_market': '瀏覽市場',
            'search_items': '搜尋商品',
            'view_recommendations': '查看推薦',
            'check_order_status': '查詢訂單狀態',
            'contact_seller': '聯繫賣家',
            'apply_arbitration': '申請仲裁',
            'check_balance': '檢查餘額',
            'recharge_wallet': '充值錢包',
            'view_transaction_history': '查看交易記錄',
            'contact_customer_service': '聯繫客服',
            'submit_ticket': '提交工單',
            'view_faq': '查看常見問題',
            'browse_help_center': '瀏覽幫助中心',
            'security_check': '安全檢查',
            'upgrade_vip': '升級VIP'
        };

        return actionMap[action] || action;
    }

    handleAction(action) {
        switch (action) {
            case 'view_profile':
                window.location.href = '/profile';
                break;
            case 'browse_market':
                window.location.href = '/market';
                break;
            case 'check_balance':
                this.sendQuickQuery('如何查詢錢包餘額？');
                break;
            case 'view_faq':
                this.sendQuickQuery('常見問題');
                break;
            default:
                this.sendQuickQuery(`我想${this.getActionText(action)}`);
        }
    }

    showTypingIndicator() {
        if (this.isTyping) return;

        this.isTyping = true;
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator';
        indicator.id = 'typingIndicator';

        const container = document.createElement('div');
        container.className = 'typing-container';

        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'typing-dots';

        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('div');
            dot.className = 'typing-dot';
            dotsContainer.appendChild(dot);
        }

        const text = document.createElement('span');
        text.className = 'typing-text';
        text.textContent = 'AI 正在思考中...';

        container.appendChild(dotsContainer);
        container.appendChild(text);
        indicator.appendChild(container);

        this.messages.appendChild(indicator);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.remove();
        }
        this.isTyping = false;
    }

    showSuggestions(actions) {
        // Remove existing suggestions
        const existing = document.querySelector('.message-suggestions');
        if (existing) {
            existing.remove();
        }

        if (actions && actions.length > 0) {
            const lastMessage = this.messages.lastElementChild;
            if (lastMessage && lastMessage.classList.contains('bot-message')) {
                const suggestions = this.createActionButtons(actions);
                lastMessage.querySelector('.message-content').insertAdjacentHTML('beforeend', suggestions);
            }
        }
    }

    scrollToBottom() {
        setTimeout(() => {
            this.messages.scrollTop = this.messages.scrollHeight;
        }, 100);
    }

    clearChat() {
        if (confirm('確定要清空對話記錄嗎？')) {
            // Keep only the initial bot message
            const initialMessage = this.messages.querySelector('.bot-message');
            this.messages.innerHTML = '';
            if (initialMessage) {
                this.messages.appendChild(initialMessage);
            }

            // Clear history
            this.messageHistory = [];
            localStorage.removeItem('chatHistory');
        }
    }

    showFeedback() {
        const rating = prompt('請為我們的客服服務打分（1-5星）：');
        if (rating && rating >= 1 && rating <= 5) {
            // Here you would send feedback to the server
            alert(`感謝您的評價！您給了我們 ${rating} 星評價。`);
        }
    }

    showNotification(count = 1) {
        this.notification.textContent = count > 99 ? '99+' : count.toString();
        this.notification.style.display = 'flex';
    }

    hideNotification() {
        this.notification.style.display = 'none';
    }

    loadMessageHistory() {
        const history = localStorage.getItem('chatHistory');
        if (history) {
            try {
                this.messageHistory = JSON.parse(history);
                // Optionally restore recent messages
            } catch (error) {
                console.error('Failed to load message history:', error);
            }
        }
    }

    saveMessageHistory() {
        try {
            localStorage.setItem('chatHistory', JSON.stringify(this.messageHistory.slice(-50))); // Keep last 50 messages
        } catch (error) {
            console.error('Failed to save message history:', error);
        }
    }

    // Public methods for external use
    showWelcomeMessage() {
        setTimeout(() => {
            this.addMessage('歡迎使用AI智能客服！有什麼可以幫助您的嗎？', 'bot');
        }, 1000);
    }

    showHelpMessage() {
        this.addMessage('以下是您可以詢問的問題類型：\n• 帳號註冊和登入問題\n• 商品資訊查詢\n• 訂單和交易狀態\n• 支付和錢包相關\n• 售後服務支援', 'bot');
    }
}

// Initialize customer service chat
document.addEventListener('DOMContentLoaded', () => {
    window.customerService = new CustomerServiceChat();

    // Auto-show welcome message after 3 seconds
    setTimeout(() => {
        if (window.customerService) {
            window.customerService.showWelcomeMessage();
        }
    }, 3000);
});

// Global functions for HTML onclick handlers
function toggleChat() {
    if (window.customerService) {
        window.customerService.toggleChat();
    }
}

function sendMessage() {
    if (window.customerService) {
        window.customerService.sendMessage();
    }
}

function clearChat() {
    if (window.customerService) {
        window.customerService.clearChat();
    }
}

function openChat() {
    if (window.customerService) {
        window.customerService.openChat();
    }
}
