<div>
    <style>
        /* AI Chat Styles */
        .chat-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            height: calc(100vh - 64px);
            margin-top: 64px;
            background: #f5f5f5;
        }

        @media (max-width: 768px) {
            .chat-container {
                grid-template-columns: 1fr;
            }
            .chat-sidebar {
                display: none;
            }
            .chat-sidebar.mobile-open {
                display: block;
                position: fixed;
                top: 64px;
                left: 0;
                width: 280px;
                height: calc(100vh - 64px);
                z-index: 40;
                box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            }
        }

        .chat-sidebar {
            background: white;
            border-right: 1px solid #e5e5ea;
            overflow-y: auto;
        }

        .sidebar-section {
            padding: 1.5rem;
            border-bottom: 1px solid #f2f2f7;
        }

        .sidebar-section h3 {
            font-size: 0.85rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chat-mode {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #666;
        }

        .chat-mode:hover {
            background: #f2f2f7;
        }

        .chat-mode.active {
            background: #e8f4f8;
            color: #0A84FF;
            font-weight: 500;
        }

        .chat-main {
            display: flex;
            flex-direction: column;
            background: white;
            height: calc(100vh - 64px);
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e5ea;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chat-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0A84FF, #00C7BE);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .chat-info h2 {
            font-size: 1rem;
            font-weight: 600;
            color: #000;
            margin: 0;
        }

        .chat-status {
            font-size: 0.8rem;
            color: #34C759;
            margin: 0;
        }

        .chat-controls {
            display: flex;
            gap: 0.5rem;
        }

        .chat-control-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #f2f2f7;
            color: #666;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-control-btn:hover {
            background: #e5e5ea;
            color: #0A84FF;
        }

        .messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f9f9f9;
        }

        .message {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            align-items: flex-start;
            opacity: 0;
            animation: messageAppear 0.3s forwards;
        }

        @keyframes messageAppear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0A84FF, #00C7BE);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .message.user .message-avatar {
            background: linear-gradient(135deg, #FF9500, #FF3B30);
        }

        .message-content {
            max-width: 70%;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .message-bubble {
            padding: 0.875rem 1.125rem;
            border-radius: 18px;
            line-height: 1.5;
            word-wrap: break-word;
            white-space: pre-line;
        }

        .message.bot .message-bubble {
            background: white;
            color: #000;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .message.user .message-bubble {
            background: #0A84FF;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 0.7rem;
            color: #999;
            padding: 0 0.5rem;
        }

        .message.user .message-time {
            text-align: right;
        }

        .typing-indicator {
            display: none;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .typing-indicator.show {
            display: flex;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
            padding: 0.875rem 1.125rem;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #999;
            animation: typingDot 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingDot {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        .input-area {
            border-top: 1px solid #e5e5ea;
            padding: 1rem 1.5rem;
            background: white;
        }

        .input-wrapper {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .chat-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e5ea;
            border-radius: 20px;
            background-color: #f9f9f9;
            color: #000;
            font-size: 0.95rem;
            font-family: inherit;
            resize: none;
            max-height: 120px;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .chat-input:focus {
            outline: none;
            border-color: #0A84FF;
            background-color: white;
        }

        .input-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #0A84FF;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .send-btn:hover {
            background-color: #0066CC;
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        .send-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .suggestions-container {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.75rem;
            padding: 0.5rem 0;
        }

        .suggestion-chip {
            padding: 0.5rem 1rem;
            background: #f2f2f7;
            border-radius: 16px;
            font-size: 0.85rem;
            color: #666;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .suggestion-chip:hover {
            background: #e8f4f8;
            color: #0A84FF;
            border-color: #0A84FF;
        }

        /* Source Badge */
        .message-source {
            font-size: 0.65rem;
            color: #999;
            padding: 0 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .message-source.local {
            color: #34C759;
        }

        .message-source.openai {
            color: #0A84FF;
        }
    </style>

    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="sidebar-section">
                <h3><i class="fas fa-comments"></i> 對話模式</h3>
                <div class="chat-mode active">
                    <i class="fas fa-robot"></i>
                    <span>AI 智能助手</span>
                </div>
            </div>

            <div class="sidebar-section">
                <h3><i class="fas fa-lightbulb"></i> 常見問題</h3>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($quickReplies as $reply)
                        <button 
                            wire:click="sendQuickReply('{{ $reply }}')"
                            class="suggestion-chip"
                            style="text-align: left; border: none; width: 100%;">
                            {{ $reply }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="chat-info">
                        <h2>AI 智能助手</h2>
                        <p class="chat-status">● 線上</p>
                    </div>
                </div>
                <div class="chat-controls">
                    <button 
                        wire:click="clearChat" 
                        class="chat-control-btn" 
                        title="清除對話"
                        wire:confirm="確定要清除所有對話記錄嗎？">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="messages-area" id="messagesArea" >
                @foreach($messages as $message)
                    <div class="message {{ $message['type'] }}">
                        <div class="message-avatar">
                            <i class="fas fa-{{ $message['type'] === 'user' ? 'user' : 'robot' }}"></i>
                        </div>
                        <div class="message-content">
                            <div class="message-bubble">
                                {{ $message['content'] }}
                            </div>
                            <div class="message-time">
                                {{ $message['time'] }}
                                @if(isset($message['source']))
                                    <span class="message-source {{ $message['source'] }}">
                                        @if($message['source'] === 'local')
                                            <i class="fas fa-database"></i> 知識庫
                                        @elseif($message['source'] === 'openai')
                                            <i class="fas fa-brain"></i> AI
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Typing Indicator -->
                @if($isTyping)
                    <div class="typing-indicator show">
                        <div class="message-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="message-content">
                            <div class="message-bubble">
                                <div class="typing-dots">
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="input-area">
                <form wire:submit.prevent="sendMessage">
                    <div class="input-wrapper">
                        <textarea
                            wire:model="messageInput"
                            class="chat-input"
                            placeholder="輸入您的問題... (Shift+Enter 換行)"
                            rows="1"
                            @keydown.enter.exact.prevent="$wire.sendMessage()"
                            @keydown.shift.enter.prevent="messageInput = messageInput + '\n'"
                            x-data
                            x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                            {{ $isTyping ? 'disabled' : '' }}
                        ></textarea>
                        <div class="input-buttons">
                            <button 
                                type="submit"
                                class="send-btn" 
                                @if($isTyping) disabled @endif >
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Quick Reply Suggestions -->
                @if(empty($messages) || count($messages) <= 1)
                    <div class="suggestions-container">
                        @foreach(array_slice($quickReplies, 0, 4) as $reply)
                            <button 
                                wire:click="sendQuickReply('{{ $reply }}')"
                                class="suggestion-chip">
                                {{ $reply }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading.flex  style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
        <div style="background:white;padding:2rem;border-radius:12px;text-align:center;">
            <img src="{{ asset('images/loading.gif') }}" width="100" alt="Loading">
            <p style="margin-top:1rem;color:#666;">處理中...</p>
        </div>
    </div>

    @script
    <script>
        // Auto scroll to bottom
        $wire.on('scroll-to-bottom', () => {
            const messagesArea = document.getElementById('messagesArea');
            if (messagesArea) {
                setTimeout(() => {
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }, 100);
            }
        });

        // Scroll on new message
        Livewire.hook('morph.updated', () => {
            const messagesArea = document.getElementById('messagesArea');
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        });

        // Initial scroll
        document.addEventListener('livewire:initialized', () => {
            const messagesArea = document.getElementById('messagesArea');
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        });
    </script>
    @endscript
</div>