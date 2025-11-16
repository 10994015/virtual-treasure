// Cross-page AI Chat Messaging System
class CrossPageChatManager {
    constructor() {
        this.chatWindows = new Map();
        this.messageQueue = [];
        this.isInitialized = false;
        this.init();
    }

    init() {
        if (this.isInitialized) return;

        // Listen for messages from other pages/windows
        window.addEventListener('message', this.handleIncomingMessage.bind(this));

        // Listen for storage changes (for same-origin cross-tab communication)
        window.addEventListener('storage', this.handleStorageChange.bind(this));

        // Broadcast our presence
        this.broadcastPresence();

        // Set up periodic cleanup
        setInterval(() => {
            this.cleanupDeadWindows();
        }, 30000); // Clean up every 30 seconds

        this.isInitialized = true;
    }

    handleIncomingMessage(event) {
        // Only accept messages from same origin for security
        if (event.origin !== window.location.origin) return;

        const { type, data, sourceId } = event.data;

        switch (type) {
            case 'CHAT_MESSAGE':
                this.handleChatMessage(data, sourceId);
                break;
            case 'CHAT_WINDOW_OPEN':
                this.registerChatWindow(sourceId, event.source);
                break;
            case 'CHAT_WINDOW_CLOSE':
                this.unregisterChatWindow(sourceId);
                break;
            case 'REQUEST_CHAT_HISTORY':
                this.sendChatHistory(event.source);
                break;
            case 'NAVIGATE_TO_PAGE':
                this.handlePageNavigation(data);
                break;
        }
    }

    handleStorageChange(event) {
        if (event.key === 'crossPageChatMessage') {
            try {
                const messageData = JSON.parse(event.newValue);
                if (messageData) {
                    this.handleChatMessage(messageData);
                }
            } catch (e) {
                console.warn('Failed to parse cross-page chat message:', e);
            }
            // Clear the storage item
            localStorage.removeItem('crossPageChatMessage');
        }
    }

    handleChatMessage(messageData, sourceId = null) {
        const { message, timestamp, page, userId } = messageData;

        // Add message to our chat if we have one
        if (window.aiChatWidget) {
            window.aiChatWidget.addMessage('user', message);
            // Process the message
            setTimeout(() => {
                window.aiChatWidget.processMessage(message)
                    .then(response => {
                        window.aiChatWidget.addMessage('bot', response);
                        // Broadcast response to other windows
                        this.broadcastMessage({
                            type: 'bot',
                            content: response,
                            timestamp: new Date(),
                            page: window.location.pathname
                        });
                    });
            }, 1000);
        }

        // Store in shared history
        this.addToSharedHistory(messageData);
    }

    broadcastPresence() {
        // Broadcast our presence to other windows
        localStorage.setItem('crossPageChatPresence', JSON.stringify({
            page: window.location.pathname,
            timestamp: Date.now(),
            hasChat: !!window.aiChatWidget
        }));

        // Clear after a short time
        setTimeout(() => {
            localStorage.removeItem('crossPageChatPresence');
        }, 1000);
    }

    broadcastMessage(messageData) {
        // Use localStorage for cross-tab communication
        localStorage.setItem('crossPageChatMessage', JSON.stringify(messageData));

        // Also try to send to registered windows
        this.chatWindows.forEach((windowRef, id) => {
            try {
                windowRef.postMessage({
                    type: 'CHAT_MESSAGE_RECEIVED',
                    data: messageData
                }, window.location.origin);
            } catch (e) {
                // Window might be closed
                this.chatWindows.delete(id);
            }
        });
    }

    registerChatWindow(windowId, windowRef) {
        this.chatWindows.set(windowId, windowRef);
    }

    unregisterChatWindow(windowId) {
        this.chatWindows.delete(windowId);
    }

    cleanupDeadWindows() {
        // Remove windows that are no longer accessible
        for (const [id, windowRef] of this.chatWindows) {
            try {
                // Try to access the window
                windowRef.location.href;
            } catch (e) {
                this.chatWindows.delete(id);
            }
        }
    }

    addToSharedHistory(messageData) {
        const history = this.getSharedHistory();
        history.push({
            ...messageData,
            id: Date.now() + Math.random()
        });

        // Keep only last 100 messages
        if (history.length > 100) {
            history.splice(0, history.length - 100);
        }

        localStorage.setItem('sharedChatHistory', JSON.stringify(history));
    }

    getSharedHistory() {
        try {
            return JSON.parse(localStorage.getItem('sharedChatHistory') || '[]');
        } catch (e) {
            return [];
        }
    }

    sendChatHistory(targetWindow) {
        const history = this.getSharedHistory();
        try {
            targetWindow.postMessage({
                type: 'CHAT_HISTORY_RESPONSE',
                data: history
            }, window.location.origin);
        } catch (e) {
            console.warn('Failed to send chat history:', e);
        }
    }

    handlePageNavigation(data) {
        const { page, message } = data;
        // Navigate to the specified page with a message
        if (message) {
            // Store the message to be processed when the page loads
            sessionStorage.setItem('pendingChatMessage', JSON.stringify(message));
        }

        // Navigate to the page
        if (page !== window.location.pathname) {
            window.location.href = page;
        }
    }

    // Public methods for external use
    sendCrossPageMessage(message, targetPage = null) {
        const messageData = {
            message,
            timestamp: new Date(),
            page: window.location.pathname,
            userId: this.getUserId()
        };

        if (targetPage) {
            // Send to specific page
            this.broadcastMessage({
                ...messageData,
                targetPage
            });
        } else {
            // Broadcast to all pages
            this.broadcastMessage(messageData);
        }
    }

    navigateWithMessage(page, message) {
        this.handlePageNavigation({ page, message });
    }

    getUserId() {
        // Get or create a user ID for tracking conversations
        let userId = localStorage.getItem('chatUserId');
        if (!userId) {
            userId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('chatUserId', userId);
        }
        return userId;
    }

    getRecentMessages(limit = 10) {
        const history = this.getSharedHistory();
        return history.slice(-limit);
    }
}

// Global functions for easy access
function sendCrossPageChatMessage(message, targetPage = null) {
    if (window.crossPageChat) {
        window.crossPageChat.sendCrossPageMessage(message, targetPage);
    }
}

function navigateToPageWithChat(page, message) {
    if (window.crossPageChat) {
        window.crossPageChat.navigateWithMessage(page, message);
    }
}

function getSharedChatHistory(limit = 10) {
    if (window.crossPageChat) {
        return window.crossPageChat.getRecentMessages(limit);
    }
    return [];
}

// Enhanced AI Chat Widget with cross-page support
class EnhancedAIChatWidget {
    constructor(options = {}) {
        this.options = {
            enableCrossPage: true,
            enableNotifications: true,
            autoOpenDelay: 10000,
            ...options
        };

        this.isOpen = false;
        this.isMinimized = false;
        this.messages = [];
        this.isTyping = false;
        this.pageContext = this.getCurrentPageContext();

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadChatHistory();
        this.setupAutoResize();
        this.showChatToggle();

        if (this.options.enableCrossPage) {
            this.initCrossPageSupport();
        }

        // Check for pending messages from navigation
        this.checkPendingMessages();
    }

    getCurrentPageContext() {
        const path = window.location.pathname;
        const contexts = {
            '/': 'home',
            '/index.html': 'home',
            '/market.html': 'market',
            '/cart.html': 'cart',
            '/checkout.html': 'checkout',
            '/ai-chat.html': 'chat',
            '/help.html': 'help'
        };
        return contexts[path] || 'general';
    }

    initCrossPageSupport() {
        if (!window.crossPageChat) {
            window.crossPageChat = new CrossPageChatManager();
        }

        // Listen for cross-page messages
        window.addEventListener('message', (event) => {
            if (event.data.type === 'CHAT_MESSAGE_RECEIVED') {
                const messageData = event.data.data;
                this.addMessage(messageData.type, messageData.content, false);
            }
        });
    }

    checkPendingMessages() {
        const pendingMessage = sessionStorage.getItem('pendingChatMessage');
        if (pendingMessage) {
            try {
                const messageData = JSON.parse(pendingMessage);
                sessionStorage.removeItem('pendingChatMessage');

                // Process the pending message
                this.addMessage('user', messageData.message);
                setTimeout(() => {
                    this.processMessage(messageData.message)
                        .then(response => {
                            this.addMessage('bot', response);
                        });
                }, 1000);
            } catch (e) {
                console.warn('Failed to process pending message:', e);
            }
        }
    }

    bindEvents() {
        const chatToggle = document.getElementById('chatToggle');
        const minimizeBtn = document.getElementById('minimizeChat');
        const closeBtn = document.getElementById('closeChat');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendMessage');

        if (chatToggle) {
            chatToggle.addEventListener('click', () => this.toggleChat());
        }

        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', () => this.minimizeChat());
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeChat());
        }

        if (chatInput) {
            chatInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            chatInput.addEventListener('input', () => {
                this.updateSendButton();
                this.autoResizeTextarea(chatInput);
            });
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }

        // Quick suggestion buttons
        document.querySelectorAll('.suggestion-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const question = btn.textContent;
                this.sendQuickQuestion(question);
            });
        });
    }

    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    openChat() {
        const chatWidget = document.getElementById('aiChatWidget');
        const chatToggle = document.getElementById('chatToggle');

        if (chatWidget) {
            chatWidget.classList.add('show');
            this.isOpen = true;
            this.isMinimized = false;
        }

        if (chatToggle) {
            chatToggle.style.display = 'none';
        }

        setTimeout(() => {
            const chatInput = document.getElementById('chatInput');
            if (chatInput) {
                chatInput.focus();
            }
        }, 300);

        this.hideNotification();

        // Notify cross-page system
        if (window.crossPageChat) {
            window.postMessage({
                type: 'CHAT_WINDOW_OPEN',
                sourceId: this.getWindowId()
            }, window.location.origin);
        }
    }

    closeChat() {
        const chatWidget = document.getElementById('aiChatWidget');
        const chatToggle = document.getElementById('chatToggle');

        if (chatWidget) {
            chatWidget.classList.remove('show');
            this.isOpen = false;
        }

        if (chatToggle) {
            chatToggle.style.display = 'flex';
        }

        // Notify cross-page system
        if (window.crossPageChat) {
            window.postMessage({
                type: 'CHAT_WINDOW_CLOSE',
                sourceId: this.getWindowId()
            }, window.location.origin);
        }
    }

    minimizeChat() {
        const chatWidget = document.getElementById('aiChatWidget');
        const chatToggle = document.getElementById('chatToggle');

        if (chatWidget) {
            chatWidget.classList.remove('show');
            this.isOpen = false;
            this.isMinimized = true;
        }

        if (chatToggle) {
            chatToggle.style.display = 'flex';
        }
    }

    showChatToggle() {
        const chatToggle = document.getElementById('chatToggle');
        if (chatToggle) {
            chatToggle.style.display = 'flex';
        }
    }

    async sendMessage() {
        const chatInput = document.getElementById('chatInput');
        const message = chatInput.value.trim();

        if (!message || this.isTyping) return;

        // Add user message
        this.addMessage('user', message);
        chatInput.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        // Process message
        const response = await this.processMessage(message);

        // Hide typing indicator and show response
        this.hideTypingIndicator();
        this.addMessage('bot', response);

        this.updateSendButton();
        this.autoResizeTextarea(chatInput);

        // Broadcast to other pages
        if (this.options.enableCrossPage && window.crossPageChat) {
            window.crossPageChat.sendCrossPageMessage(message);
        }
    }

    async sendQuickQuestion(question) {
        const chatInput = document.getElementById('chatInput');
        if (chatInput) {
            chatInput.value = question;
            this.sendMessage();
        }
    }

    async processMessage(message) {
        await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));

        // Context-aware responses based on current page
        switch (this.pageContext) {
            case 'market':
                return this.processMarketMessage(message);
            case 'cart':
                return this.processCartMessage(message);
            case 'checkout':
                return this.processCheckoutMessage(message);
            default:
                return this.processGeneralMessage(message);
        }
    }

    processMarketMessage(message) {
        const lowerMessage = message.toLowerCase();

        if (lowerMessage.includes('推薦') || lowerMessage.includes('熱門')) {
            return '在市場頁面，我可以為您推薦：<br>• 最新上架的遊戲道具<br>• 熱銷商品<br>• 特價優惠商品<br><br>您想找什麼類型的商品呢？';
        } else if (lowerMessage.includes('搜索') || lowerMessage.includes('找')) {
            return '您可以在市場頁面的搜索框中輸入關鍵字來查找商品。或者告訴我您想要找什麼，我可以為您提供建議！';
        } else if (lowerMessage.includes('價格') || lowerMessage.includes('貴')) {
            return '我們的商品價格都很實惠！建議您：<br>1. 查看商品詳情頁面的價格說明<br>2. 關注限時優惠活動<br>3. 加入會員享受更多折扣<br><br>需要我幫您找優惠商品嗎？';
        }

        return '很高興在市場頁面為您服務！有什麼可以幫助您的嗎？我可以幫您尋找商品、解答購買問題，或提供商品推薦。';
    }

    processCartMessage(message) {
        const lowerMessage = message.toLowerCase();

        if (lowerMessage.includes('結帳') || lowerMessage.includes('付款')) {
            return '要結帳付款很簡單：<br>1. 確認購物車中的商品<br>2. 點擊「前往結帳」按鈕<br>3. 填寫收貨資訊<br>4. 選擇付款方式<br>5. 完成付款<br><br>需要我幫您檢查購物車嗎？';
        } else if (lowerMessage.includes('修改') || lowerMessage.includes('數量')) {
            return '修改商品數量：<br>• 點擊商品旁的數量控制按鈕<br>• 或直接在數量輸入框中修改<br>• 系統會自動更新總價<br><br>需要幫助嗎？';
        } else if (lowerMessage.includes('刪除') || lowerMessage.includes('移除')) {
            return '刪除購物車商品：<br>• 點擊商品旁的刪除按鈕<br>• 或清空整個購物車<br><br>請確認是否真的要刪除？';
        }

        return '在購物車頁面，我可以幫助您：<br>• 修改商品數量<br>• 刪除不需要的商品<br>• 結帳付款指導<br>• 計算總價<br><br>您需要什麼幫助？';
    }

    processCheckoutMessage(message) {
        const lowerMessage = message.toLowerCase();

        if (lowerMessage.includes('付款') || lowerMessage.includes('支付')) {
            return '我們支援多種付款方式：<br>• 信用卡/金融卡<br>• PayPal<br>• 加密貨幣<br>• 銀行轉帳<br><br>請選擇最適合您的付款方式。';
        } else if (lowerMessage.includes('地址') || lowerMessage.includes('收貨')) {
            return '收貨資訊填寫：<br>• 請填寫完整的收貨地址<br>• 確認聯絡電話和郵件<br>• 選擇正確的國家和城市<br><br>地址資訊很重要，請仔細檢查！';
        } else if (lowerMessage.includes('優惠') || lowerMessage.includes('折扣')) {
            return '使用優惠券：<br>• 在結帳頁面查看可用優惠券<br>• 輸入優惠券代碼<br>• 系統會自動計算折扣<br><br>需要我幫您查看優惠活動嗎？';
        }

        return '在結帳頁面，我可以幫助您：<br>• 選擇付款方式<br>• 填寫收貨資訊<br>• 使用優惠券<br>• 確認訂單詳情<br><br>遇到問題嗎？我來幫您解決！';
    }

    processGeneralMessage(message) {
        const lowerMessage = message.toLowerCase();

        if (lowerMessage.includes('商品') && lowerMessage.includes('購買')) {
            return '購買商品很簡單！<br><br>1. 在市場頁面瀏覽商品<br>2. 點擊商品加入購物車<br>3. 進入購物車查看商品<br>4. 點擊「前往結帳」<br>5. 填寫收貨資訊並完成付款<br><br>如果需要幫助，我可以為您推薦熱門商品！';
        } else if (lowerMessage.includes('訂單') && lowerMessage.includes('查看')) {
            return '查看訂單狀態：<br><br>• 登入您的帳號<br>• 進入個人中心<br>• 點擊「我的訂單」<br><br>您也可以直接提供訂單編號，我來幫您查詢具體狀態。';
        } else if (lowerMessage.includes('退貨') || lowerMessage.includes('退款')) {
            return '退貨政策說明：<br><br>• 商品到貨7天內可申請退貨<br>• 商品必須保持原始包裝<br>• 數位商品一經交付不接受退貨<br>• 退貨運費由買家承擔<br><br>如需退貨，請聯絡客服獲取協助。';
        } else if (lowerMessage.includes('客服') || lowerMessage.includes('聯絡')) {
            return '聯絡客服方式：<br><br>• 在線客服：現在就與我對話<br>• 電話客服：0800-123-456<br>• 電子郵件：service@example.com<br>• 工作時間：週一至週五 9:00-18:00<br><br>我會盡快為您處理！';
        }

        return '感謝您的提問！我會盡力為您提供幫助。如果您有具體的問題，可以選擇上方的建議按鈕，或直接告訴我您需要什麼協助。';
    }

    addMessage(type, content, shouldSave = true) {
        const messagesContainer = document.getElementById('chatMessages');
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message appear`;

        const avatar = type === 'bot' ? 'fas fa-robot' : 'fas fa-user';
        const avatarBg = type === 'bot' ? '#0A84FF' : '#007AFF';

        messageDiv.innerHTML = `
            <div class="message-avatar" style="background: ${avatarBg}">
                <i class="${avatar}"></i>
            </div>
            <div class="message-content">
                <div class="message-text">${content}</div>
                <div class="message-time">${this.getCurrentTime()}</div>
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        if (shouldSave) {
            this.messages.push({
                type,
                content,
                timestamp: new Date(),
                page: this.pageContext
            });
            this.saveChatHistory();
        }
    }

    showTypingIndicator() {
        this.isTyping = true;
        const messagesContainer = document.getElementById('chatMessages');

        if (messagesContainer) {
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typingIndicator';
            typingDiv.className = 'message bot-message';
            typingDiv.innerHTML = `
                <div class="message-avatar" style="background: #0A84FF">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-text">
                        <div class="typing-dots">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                </div>
            `;

            messagesContainer.appendChild(typingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        this.updateSendButton();
    }

    hideTypingIndicator() {
        this.isTyping = false;
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
        this.updateSendButton();
    }

    updateSendButton() {
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendMessage');

        if (sendBtn) {
            const hasContent = chatInput && chatInput.value.trim().length > 0;
            const isEnabled = hasContent && !this.isTyping;

            sendBtn.disabled = !isEnabled;
            sendBtn.style.opacity = isEnabled ? '1' : '0.5';
        }
    }

    autoResizeTextarea(textarea) {
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }
    }

    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('zh-TW', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    saveChatHistory() {
        try {
            localStorage.setItem('aiChatHistory', JSON.stringify(this.messages.slice(-50)));
        } catch (e) {
            console.warn('Failed to save chat history:', e);
        }
    }

    loadChatHistory() {
        try {
            const history = JSON.parse(localStorage.getItem('aiChatHistory') || '[]');
            const messagesContainer = document.getElementById('chatMessages');

            if (messagesContainer && history.length > 0) {
                // Clear default message
                messagesContainer.innerHTML = '';

                // Load recent messages (last 10)
                history.slice(-10).forEach(msg => {
                    this.addMessage(msg.type, msg.content, false);
                });
            }
        } catch (e) {
            console.warn('Failed to load chat history:', e);
        }
    }

    showNotification(count = 1) {
        const notification = document.getElementById('chatNotification');
        if (notification) {
            notification.textContent = count > 9 ? '9+' : count;
            notification.classList.remove('hidden');
        }
    }

    hideNotification() {
        const notification = document.getElementById('chatNotification');
        if (notification) {
            notification.classList.add('hidden');
        }
    }

    getWindowId() {
        if (!this.windowId) {
            this.windowId = 'window_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        return this.windowId;
    }

    // Public methods
    sendMessageToUser(message) {
        this.addMessage('bot', message);
    }

    getChatHistory() {
        return this.messages;
    }
}

// Initialize enhanced chat widget
document.addEventListener('DOMContentLoaded', function() {
    window.aiChatWidget = new EnhancedAIChatWidget({
        enableCrossPage: true,
        enableNotifications: true,
        autoOpenDelay: 10000
    });

    // Initialize cross-page manager
    window.crossPageChat = new CrossPageChatManager();

    // Auto-show chat toggle after delay
    setTimeout(() => {
        if (window.aiChatWidget && !window.aiChatWidget.isOpen) {
            window.aiChatWidget.showChatToggle();
        }
    }, 5000);

    // Show welcome notification
    setTimeout(() => {
        if (window.aiChatWidget && !window.aiChatWidget.isOpen) {
            window.aiChatWidget.showNotification();
        }
    }, 10000);
});
