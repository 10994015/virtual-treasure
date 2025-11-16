/**
 * 訊息系統 - 前端邏輯
 * 功能：1-2-1 訊息對話、訊息歷史、未讀提醒、簡單通知
 */

class MessagingSystem {
    constructor() {
        this.currentUser = null;
        this.currentChatWith = null;
        this.conversations = this.loadConversations();
        this.unreadCounts = this.loadUnreadCounts();
        this.allContacts = []; // 所有可用的聯絡人
        
        // 議價模式狀態
        this.bargainState = {
            isActive: false,
            currentPhase: null, // 'buyer_propose', 'seller_review', 'seller_propose', 'buyer_confirm'
            buyerPrice: null,
            sellerPrice: null,
            historyMin: 800,
            historyMax: 1200
        };
        
        this.loadContactList();
        this.initializeUI();
        this.setupEventListeners();
        this.setupBargainListeners();
        this.startNotificationCheck();
    }

    /**
     * 初始化 UI
     */
    initializeUI() {
        this.currentUser = localStorage.getItem('username') || '買家用戶';
        
        // 檢查元素是否存在再設置
        const usernameEl = document.getElementById('username');
        if (usernameEl) {
            usernameEl.textContent = this.currentUser;
        }

        // 檢查登入狀態
        const token = localStorage.getItem('token');
        const logoutBtn = document.getElementById('logoutBtn');
        const loginBtn = document.getElementById('loginBtn');
        
        if (token) {
            if (logoutBtn) logoutBtn.style.display = 'inline-block';
            if (loginBtn) loginBtn.style.display = 'none';
        }

        this.renderChatList();
    }

    /**
     * 設置事件監聽器
     */
    setupEventListeners() {
        // 搜尋輸入框
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.filterChats(e.target.value);
        });

        // 訊息發送
        document.getElementById('sendBtn').addEventListener('click', () => {
            this.sendMessage();
        });

        document.getElementById('messageInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // 自動調整 textarea 高度
        document.getElementById('messageInput').addEventListener('input', (e) => {
            e.target.style.height = 'auto';
            e.target.style.height = Math.min(e.target.scrollHeight, 100) + 'px';
        });

        // 清除聊天記錄
        const clearChatBtn = document.getElementById('clearChatBtn');
        if (clearChatBtn) {
            clearChatBtn.addEventListener('click', () => {
                if (confirm('確定要清除這段對話的所有訊息嗎？')) {
                    this.clearChat();
                }
            });
        }

        // 登出
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                localStorage.removeItem('token');
                localStorage.removeItem('username');
                location.reload();
            });
        }

        // 移動菜單切換
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('show');
            });
        }
    }

    /**
     * 加載聯絡人列表
     */
    loadContactList() {
        // 從對話歷史中提取聯絡人
        this.allContacts = [];

        // 確保已有訊息記錄的聯絡人都在列表中
        for (let conversationId in this.conversations) {
            const parts = conversationId.split('_');
            if (parts.length >= 3) {
                const contactId = parts[2];
                if (!this.allContacts.find(c => c.id == contactId)) {
                    this.allContacts.push({
                        id: contactId,
                        name: `聯絡人 ${contactId}`,
                        avatar: contactId.substring(0, 1).toUpperCase(),
                        type: 'unknown',
                        status: 'offline'
                    });
                }
            }
        }

        // 如果沒有聯絡人，添加演示聯絡人供測試
        if (this.allContacts.length === 0) {
            this.allContacts = [
                { id: 1, name: '熱心賣家', avatar: 'S', type: 'seller', status: 'online' },
                { id: 2, name: '王買家', avatar: 'W', type: 'buyer', status: 'offline' }
            ];
            
            // 為測試賣家添加歡迎訊息
            const conversationId = this.getConversationId(1);
            this.conversations[conversationId] = [
                {
                    sender: '熱心賣家',
                    text: '您好！歡迎使用議價功能，可以點擊握手圖示 🤝 開始議價。',
                    timestamp: Date.now(),
                    read: false,
                    id: 'welcome_msg'
                }
            ];
            this.saveConversations();
        }
    }

    /**
     * 渲染聊天列表
     */
    renderChatList() {
        const chatList = document.getElementById('chatList');
        chatList.innerHTML = '';

        // 獲取所有有訊息的聯絡人
        const activeContacts = this.getActiveContacts();

        if (activeContacts.length === 0) {
            chatList.innerHTML = `
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>暫無訊息</p>
                </div>
            `;
            return;
        }

        activeContacts.forEach(contact => {
            const conversationId = this.getConversationId(contact.id);
            const conversation = this.conversations[conversationId] || [];
            const lastMessage = conversation[conversation.length - 1];
            const unreadCount = this.unreadCounts[conversationId] || 0;

            const isActive = this.currentChatWith?.id === contact.id;
            const chatItem = document.createElement('div');
            chatItem.className = `chat-item ${isActive ? 'active' : ''}`;
            chatItem.innerHTML = `
                <div class="chat-item-avatar">${contact.avatar}</div>
                <div class="chat-item-info">
                    <div class="chat-item-header">
                        <span class="chat-item-name">${contact.name}</span>
                        <span class="chat-item-time">${lastMessage ? this.formatTime(lastMessage.timestamp) : ''}</span>
                    </div>
                    <div class="chat-item-message">${lastMessage ? this.truncateMessage(lastMessage.text) : '開始對話'}</div>
                </div>
                ${unreadCount > 0 ? `<div class="chat-item-unread">${unreadCount}</div>` : ''}
            `;

            chatItem.addEventListener('click', () => {
                this.openChat(contact);
            });

            chatList.appendChild(chatItem);
        });
    }

    /**
     * 獲取活躍聯絡人列表
     */
    getActiveContacts() {
        return this.allContacts.filter(contact => {
            const conversationId = this.getConversationId(contact.id);
            return this.conversations[conversationId] && this.conversations[conversationId].length > 0;
        }).sort((a, b) => {
            // 按最後訊息時間排序
            const conversationIdA = this.getConversationId(a.id);
            const conversationIdB = this.getConversationId(b.id);
            const messagesA = this.conversations[conversationIdA] || [];
            const messagesB = this.conversations[conversationIdB] || [];
            
            const timeA = messagesA[messagesA.length - 1]?.timestamp || 0;
            const timeB = messagesB[messagesB.length - 1]?.timestamp || 0;
            
            return timeB - timeA;
        });
    }

    /**
     * 打開聊天對話
     */
    openChat(contact) {
        this.currentChatWith = contact;
        
        // 清除未讀計數
        const conversationId = this.getConversationId(contact.id);
        this.unreadCounts[conversationId] = 0;
        this.saveUnreadCounts();
        
        // 更新 UI
        document.getElementById('emptyChatState').style.display = 'none';
        document.getElementById('chatContent').style.display = 'flex';
        document.getElementById('currentUserName').textContent = contact.name;
        document.getElementById('currentUserAvatar').textContent = contact.avatar;
        document.getElementById('currentUserStatus').textContent = 
            contact.status === 'online' ? '在線' : '離線';
        
        this.renderMessages();
        this.renderChatList();
        
        // 滾動到底部
        setTimeout(() => {
            const messagesDiv = document.getElementById('chatMessages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }, 0);

        // 清空輸入框
        document.getElementById('messageInput').value = '';
        document.getElementById('messageInput').style.height = 'auto';
    }

    /**
     * 渲染訊息
     */
    renderMessages() {
        const messagesDiv = document.getElementById('chatMessages');
        messagesDiv.innerHTML = '';

        if (!this.currentChatWith) return;

        const conversationId = this.getConversationId(this.currentChatWith.id);
        const messages = this.conversations[conversationId] || [];

        if (messages.length === 0) {
            messagesDiv.innerHTML = `
                <div class="empty-chat">
                    <i class="fas fa-comments"></i>
                    <p>開始對話吧！</p>
                </div>
            `;
            return;
        }

        // 按日期分組訊息
        let lastDate = null;
        messages.forEach(message => {
            const messageDate = new Date(message.timestamp).toLocaleDateString('zh-TW');
            
            // 如果日期改變，添加日期標籤
            if (lastDate !== messageDate) {
                const dateDiv = document.createElement('div');
                dateDiv.className = 'text-center my-4';
                dateDiv.innerHTML = `<span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">${messageDate}</span>`;
                messagesDiv.appendChild(dateDiv);
                lastDate = messageDate;
            }

            const messageGroup = document.createElement('div');
            messageGroup.className = `message-group ${message.sender === this.currentUser ? 'sent' : 'received'}`;

            const messageTime = this.formatTime(message.timestamp);
            const messageElement = document.createElement('div');
            messageElement.className = `message ${message.sender === this.currentUser ? 'sent' : 'received'}`;
            messageElement.innerHTML = `
                <span>${message.text}</span>
                <span class="message-status">
                    ${message.sender === this.currentUser && message.read ? '✓✓' : (message.sender === this.currentUser ? '✓' : '')}
                </span>
            `;

            const timeElement = document.createElement('div');
            timeElement.className = 'message-time';
            timeElement.textContent = messageTime;

            messageGroup.appendChild(timeElement);
            messageGroup.appendChild(messageElement);
            messagesDiv.appendChild(messageGroup);
        });
    }

    /**
     * 發送訊息
     */
    sendMessage() {
        if (!this.currentChatWith) {
            this.showNotification('請選擇一個聯絡人', 'error');
            return;
        }

        const inputElement = document.getElementById('messageInput');
        const text = inputElement.value.trim();

        if (!text) return;

        const conversationId = this.getConversationId(this.currentChatWith.id);
        
        // 確保對話存在
        if (!this.conversations[conversationId]) {
            this.conversations[conversationId] = [];
        }

        // 創建訊息物件
        const message = {
            sender: this.currentUser,
            text: text,
            timestamp: Date.now(),
            read: false,
            id: Math.random().toString(36).substr(2, 9)
        };

        // 添加到對話
        this.conversations[conversationId].push(message);
        
        // 保存到本地存儲
        this.saveConversations();
        
        // 清空輸入框
        inputElement.value = '';
        inputElement.style.height = 'auto';

        // 更新 UI
        this.renderMessages();
        this.renderChatList();

        // 滾動到底部
        setTimeout(() => {
            const messagesDiv = document.getElementById('chatMessages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }, 0);

        // 模擬對方回覆
        this.simulateReply();
    }

    /**
     * 模擬對方回覆
     */
    simulateReply() {
        setTimeout(() => {
            const replies = [
                '好的，我知道了！',
                '感謝您的訊息',
                '請問還有其他問題嗎？',
                '稍後回覆您',
                '了解，謝謝！',
                '可以的，沒問題',
            ];

            const randomReply = replies[Math.floor(Math.random() * replies.length)];
            const conversationId = this.getConversationId(this.currentChatWith.id);

            const replyMessage = {
                sender: this.currentChatWith.name,
                text: randomReply,
                timestamp: Date.now() + 1000,
                read: false,
                id: Math.random().toString(36).substr(2, 9)
            };

            this.conversations[conversationId].push(replyMessage);
            this.saveConversations();

            // 增加未讀計數（如果不在當前對話窗口）
            // 這裡由於正在對話中，不增加未讀計數

            this.renderMessages();
            
            // 發送通知
            this.showNotification(
                `${this.currentChatWith.name}: ${randomReply}`,
                'message'
            );

            // 滾動到底部
            setTimeout(() => {
                const messagesDiv = document.getElementById('chatMessages');
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }, 0);
        }, 500 + Math.random() * 1500);
    }

    /**
     * 清除聊天記錄
     */
    clearChat() {
        if (!this.currentChatWith) return;

        const conversationId = this.getConversationId(this.currentChatWith.id);
        delete this.conversations[conversationId];
        delete this.unreadCounts[conversationId];

        this.saveConversations();
        this.saveUnreadCounts();

        this.renderMessages();
        this.renderChatList();
        
        this.showNotification('已清除聊天記錄', 'success');
    }

    /**
     * 過濾聊天列表
     */
    filterChats(keyword) {
        const chatList = document.getElementById('chatList');
        const items = chatList.querySelectorAll('.chat-item');

        items.forEach(item => {
            const name = item.querySelector('.chat-item-name').textContent.toLowerCase();
            const message = item.querySelector('.chat-item-message').textContent.toLowerCase();

            if (name.includes(keyword.toLowerCase()) || message.includes(keyword.toLowerCase())) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    /**
     * 獲取對話 ID
     */
    getConversationId(contactId) {
        const currentUserId = localStorage.getItem('userId') || 'current_user';
        return `conv_${Math.min(currentUserId, contactId)}_${Math.max(currentUserId, contactId)}`;
    }

    /**
     * 格式化時間
     */
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();

        // 同一天
        if (date.toDateString() === now.toDateString()) {
            return date.toLocaleTimeString('zh-TW', { hour: '2-digit', minute: '2-digit' });
        }

        // 昨天
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (date.toDateString() === yesterday.toDateString()) {
            return '昨天';
        }

        // 本周內
        if (now.getTime() - date.getTime() < 7 * 24 * 60 * 60 * 1000) {
            const days = Math.floor((now.getTime() - date.getTime()) / (24 * 60 * 60 * 1000));
            return `${days}天前`;
        }

        // 其他
        return date.toLocaleDateString('zh-TW');
    }

    /**
     * 截斷訊息
     */
    truncateMessage(text) {
        return text.length > 30 ? text.substr(0, 30) + '...' : text;
    }

    /**
     * 保存對話到本地存儲
     */
    saveConversations() {
        localStorage.setItem('messaging_conversations', JSON.stringify(this.conversations));
    }

    /**
     * 加載對話從本地存儲
     */
    loadConversations() {
        const stored = localStorage.getItem('messaging_conversations');
        return stored ? JSON.parse(stored) : this.getDefaultConversations();
    }

    /**
     * 獲取默認對話（空狀態）
     */
    getDefaultConversations() {
        return {};
    }

    /**
     * 保存未讀計數
     */
    saveUnreadCounts() {
        localStorage.setItem('messaging_unread', JSON.stringify(this.unreadCounts));
    }

    /**
     * 加載未讀計數
     */
    loadUnreadCounts() {
        const stored = localStorage.getItem('messaging_unread');
        return stored ? JSON.parse(stored) : {};
    }

    /**
     * 顯示通知
     */
    showNotification(message, type = 'info') {
        const container = document.getElementById('notificationContainer');
        
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.backgroundColor = {
            'success': '#34C759',
            'error': '#FF3B30',
            'message': '#0A84FF',
            'info': '#5856D6'
        }[type] || '#5856D6';
        
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-${
                    type === 'success' ? 'check-circle' :
                    type === 'error' ? 'exclamation-circle' :
                    type === 'message' ? 'envelope' : 'info-circle'
                }"></i>
                <span>${message}</span>
            </div>
        `;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideIn 0.3s ease-out reverse';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * 設置議價模式監聽器
     */
    setupBargainListeners() {
        // 使用事件委託來處理動態元素
        document.addEventListener('click', (e) => {
            // 切換議價模式
            if (e.target.closest('#toggleBargain')) {
                this.toggleBargainMode();
            }
            
            // 關閉議價面板
            else if (e.target.closest('#closeBargainBtn')) {
                this.closeBargainMode();
            }
            
            // 買家提交議價
            else if (e.target.closest('#submitBuyerPrice')) {
                const price = document.getElementById('buyerPrice').value;
                if (price) {
                    this.submitBuyerPrice(parseFloat(price));
                }
            }
            
            // 賣家同意價格
            else if (e.target.closest('#acceptPrice')) {
                this.dealCompleted(this.bargainState.buyerPrice);
            }
            
            // 賣家拒絕並反議價
            else if (e.target.closest('#rejectPrice')) {
                this.rejectAndCounterBargain();
            }
            
            // 賣家提交反議價
            else if (e.target.closest('#submitSellerPrice')) {
                const price = document.getElementById('sellerPrice').value;
                if (price) {
                    this.submitSellerPrice(parseFloat(price));
                }
            }
            
            // 買家同意成交
            else if (e.target.closest('#confirmDeal')) {
                this.dealCompleted(this.bargainState.sellerPrice);
            }
            
            // 買家繼續議價
            else if (e.target.closest('#continueNegotiate')) {
                this.startBargainPhase('buyer_propose');
            }
        });
    }

    /**
     * 切換議價模式
     */
    toggleBargainMode() {
        if (!this.currentChatWith) {
            this.showNotification('請先選擇聯絡人', 'error');
            return;
        }

        this.bargainState.isActive = !this.bargainState.isActive;
        
        if (this.bargainState.isActive) {
            document.getElementById('bargainPanel').style.display = 'block';
            // 隨機決定由誰開始議價 - 為了演示，假設買家開始
            this.startBargainPhase('buyer_propose');
        } else {
            this.closeBargainMode();
        }
    }

    /**
     * 關閉議價模式
     */
    closeBargainMode() {
        this.bargainState.isActive = false;
        document.getElementById('bargainPanel').style.display = 'none';
        this.bargainState.currentPhase = null;
        this.resetBargainUI();
    }

    /**
     * 開始議價階段
     */
    startBargainPhase(phase) {
        this.bargainState.currentPhase = phase;
        this.resetBargainUI();

        if (phase === 'buyer_propose') {
            // 買家提議價格
            document.getElementById('buyerBargain').style.display = 'block';
            document.getElementById('buyerPrice').focus();
        } else if (phase === 'seller_review') {
            // 賣家審查買家的價格
            document.getElementById('sellerBargain').style.display = 'block';
            document.getElementById('displayBuyerPrice').textContent = `NT$${this.bargainState.buyerPrice}`;
        } else if (phase === 'seller_propose') {
            // 賣家提議價格
            document.getElementById('counterBargain').style.display = 'block';
            document.getElementById('sellerPrice').focus();
        } else if (phase === 'buyer_confirm') {
            // 買家確認賣家的價格
            document.getElementById('buyerConfirm').style.display = 'block';
            document.getElementById('displaySellerPrice').textContent = `NT$${this.bargainState.sellerPrice}`;
        }
    }

    /**
     * 重置議價UI
     */
    resetBargainUI() {
        document.getElementById('buyerBargain').style.display = 'none';
        document.getElementById('sellerBargain').style.display = 'none';
        document.getElementById('counterBargain').style.display = 'none';
        document.getElementById('buyerConfirm').style.display = 'none';
        
        document.getElementById('buyerPrice').value = '';
        document.getElementById('sellerPrice').value = '';
    }

    /**
     * 買家提交議價
     */
    submitBuyerPrice(price) {
        // 驗證價格
        if (price < this.bargainState.historyMin - 100 || price > this.bargainState.historyMax + 100) {
            this.showNotification('價格應在歷史區間附近 (NT$700-1300)', 'error');
            return;
        }

        this.bargainState.buyerPrice = price;
        
        // 添加訊息到聊天區域
        const message = {
            sender: this.currentUser,
            text: `提議購買價格：NT$${price}`,
            timestamp: Date.now(),
            read: false,
            id: Math.random().toString(36).substr(2, 9),
            type: 'bargain_proposal'
        };
        
        this.addMessageToChat(message);
        
        // 轉到賣家審查階段
        setTimeout(() => {
            this.startBargainPhase('seller_review');
            this.showNotification(`等待賣家回應...`, 'info');
        }, 500);
    }

    /**
     * 賣家拒絕並反議價
     */
    rejectAndCounterBargain() {
        this.showNotification('轉到賣家反議價...', 'info');
        
        // 添加拒絕訊息
        const rejectMsg = {
            sender: this.currentChatWith.name,
            text: `拒絕您的價格 NT$${this.bargainState.buyerPrice}，我來反議價`,
            timestamp: Date.now(),
            read: false,
            id: Math.random().toString(36).substr(2, 9),
            type: 'bargain_reject'
        };
        
        this.addMessageToChat(rejectMsg);
        
        setTimeout(() => {
            this.startBargainPhase('seller_propose');
        }, 800);
    }

    /**
     * 賣家提交反議價
     */
    submitSellerPrice(price) {
        // 驗證價格應該高於買家價格
        if (price <= this.bargainState.buyerPrice) {
            this.showNotification('反議價應高於買家議價', 'error');
            return;
        }

        this.bargainState.sellerPrice = price;
        
        // 添加訊息到聊天區域
        const message = {
            sender: this.currentChatWith.name,
            text: `反議購買價格：NT$${price}`,
            timestamp: Date.now(),
            read: false,
            id: Math.random().toString(36).substr(2, 9),
            type: 'bargain_counter'
        };
        
        this.addMessageToChat(message);
        
        // 轉到買家確認階段
        setTimeout(() => {
            this.startBargainPhase('buyer_confirm');
            this.showNotification(`等待買家回應...`, 'info');
        }, 500);
    }

    /**
     * 成交
     */
    dealCompleted(finalPrice) {
        // 添加成交訊息
        const dealMsg = {
            sender: this.currentUser,
            text: `✅ 成交！最終交易價格：NT$${finalPrice}`,
            timestamp: Date.now(),
            read: false,
            id: Math.random().toString(36).substr(2, 9),
            type: 'deal_completed'
        };
        
        this.addMessageToChat(dealMsg);
        this.showNotification('🎉 交易成功！', 'success');
        
        // 關閉議價模式
        setTimeout(() => {
            this.closeBargainMode();
        }, 1000);
    }

    /**
     * 添加訊息到聊天
     */
    addMessageToChat(message) {
        const conversationId = this.getConversationId(this.currentChatWith.id);
        
        if (!this.conversations[conversationId]) {
            this.conversations[conversationId] = [];
        }
        
        this.conversations[conversationId].push(message);
        this.saveConversations();
        this.renderMessages();
    }

    /**
     * 啟動通知檢查
     */
    startNotificationCheck() {
        // 每30秒檢查一次新訊息
        setInterval(() => {
            this.checkForNewMessages();
        }, 30000);
    }

    /**
     * 檢查新訊息
     */
    checkForNewMessages() {
        // 實際應從後端 API 檢查
        // 這裡只是演示邏輯
        const unreadTotal = Object.values(this.unreadCounts).reduce((a, b) => a + b, 0);
        
        if (unreadTotal > 0) {
            // 更新頁面標題
            document.title = `訊息 (${unreadTotal}) - 虛擬寶物交易平台`;
        } else {
            document.title = '訊息 - 虛擬寶物交易平台';
        }
    }
}

// 當頁面加載完成時初始化
document.addEventListener('DOMContentLoaded', () => {
    window.messagingSystem = new MessagingSystem();
});

