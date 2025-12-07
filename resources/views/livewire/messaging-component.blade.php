<div class="livewire-messaging-component">
    <style>
        .livewire-messaging-component {
            display: block;
            width: 100%;
            height: 100%;
        }

        .messaging-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            height: calc(100vh - 64px);
            background: #f5f5f5;
        }

        .chat-sidebar {
            background: white;
            border-right: 1px solid #e5e5ea;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e5ea;
        }

        .chat-sidebar-search {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e5ea;
            border-radius: 8px;
            background-color: #f2f2f7;
            font-size: 0.9rem;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            padding: 1rem;
            border-bottom: 1px solid #f2f2f7;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .chat-item:hover {
            background-color: #f9f9f9;
        }

        .chat-item.active {
            background-color: #e8f4f8;
            border-left: 3px solid #0A84FF;
        }

        .chat-item-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0A84FF, #00C7BE);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .chat-item-content {
            flex: 1;
            min-width: 0;
        }

        .chat-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .chat-item-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-time {
            font-size: 0.75rem;
            color: #999;
            flex-shrink: 0;
            margin-left: 0.5rem;
        }

        .chat-item-message {
            font-size: 0.85rem;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.25rem;
        }

        .chat-item-product {
            font-size: 0.75rem;
            color: #0A84FF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-badge {
            display: inline-block;
            min-width: 20px;
            height: 20px;
            background: #FF3B30;
            color: white;
            border-radius: 10px;
            font-size: 0.7rem;
            text-align: center;
            line-height: 20px;
            padding: 0 6px;
        }

        .chat-main {
            display: flex;
            flex-direction: column;
            background: white;
            height: 100%;
        }

        .chat-messages {
            flex: 1;
            overflow-y: scroll !important;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: #f9f9f9;
            max-height: 500px !important;
        }

        .message-group {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .message-group.sent {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0A84FF, #00C7BE);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .message-content {
            max-width: 60%;
            width: auto;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .message-content.bargain-message-content {
            min-width: 40%;
        }

        .message-bubble {
            padding: 0.75rem 1rem;
            border-radius: 18px;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .message-group.received .message-bubble {
            background: white;
            color: #000;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .message-group.sent .message-bubble {
            background: #0A84FF;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 0.7rem;
            color: #999;
            padding: 0 0.5rem;
        }

        .message-group.sent .message-time {
            text-align: right;
        }

        .message-system {
            text-align: center;
            padding: 0.5rem 1rem;
            margin: 0.5rem 0;
        }

        .message-system-content {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(10, 132, 255, 0.1);
            border-radius: 12px;
            font-size: 0.85rem;
            color: #0A84FF;
        }

        .bargain-message {
            background: linear-gradient(135deg, #fff5e6 0%, #ffe6cc 100%);
            border-left: 4px solid #FF9500;
            padding: 1rem;
            border-radius: 12px;
            margin: 0.5rem 0;
        }

        .bargain-message-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #FF9500;
            font-size: 0.95rem;
        }

        .bargain-details {
            background: rgba(255, 255, 255, 0.6);
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .bargain-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            font-size: 0.9rem;
        }

        .bargain-detail-row.total {
            border-top: 2px solid #FF9500;
            margin-top: 0.5rem;
            padding-top: 0.75rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #FF9500;
        }

        .bargain-detail-label {
            color: #666;
        }

        .bargain-detail-value {
            color: #333;
            font-weight: 600;
        }

        .bargain-detail-row.total .bargain-detail-value {
            color: #FF9500;
            font-size: 1.3rem;
        }

        .chat-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e5ea;
            border-radius: 20px;
            background-color: #f2f2f7;
            color: #000;
            font-size: 0.95rem;
            font-family: inherit;
            resize: none;
            max-height: 100px;
            transition: border-color 0.2s;
        }

        .chat-input:focus {
            outline: none;
            border-color: #0A84FF;
            background-color: white;
        }

        .bargain-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .bargain-input-wrapper {
            flex: 1;
            position: relative;
        }

        .bargain-input-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 0.9rem;
            pointer-events: none;
            font-weight: 500;
        }

        .bargain-input {
            width: 100%;
            padding: 0.75rem 0.75rem 0.75rem 2.5rem;
            border: 2px solid #e5e5ea;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .bargain-input:focus {
            outline: none;
            border-color: #0A84FF;
        }

        .bargain-input-suffix {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 0.85rem;
            pointer-events: none;
        }

        .bargain-total-preview {
            background: linear-gradient(135deg, #e6f3ff 0%, #cce7ff 100%);
            border: 2px solid #0A84FF;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin: 0.75rem 0;
        }

        .bargain-total-preview-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .bargain-total-preview-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0A84FF;
        }

        @media (max-width: 768px) {
            .messaging-container {
                grid-template-columns: 1fr;
            }

            .chat-sidebar {
                display: none;
            }

            .chat-sidebar.mobile-show {
                display: flex;
            }
        }

        .message-content img {
            transition: transform 0.2s;
        }

        .message-content img:hover {
            transform: scale(1.02);
        }

        label[for^="imageUpload"]:hover {
            background-color: #f0f8ff !important;
            border-color: #0A84FF !important;
        }

        button:disabled,
        textarea:disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
        }
    </style>

    <div class="messaging-container">
        <!-- 聊天列表側邊欄 -->
        <div class="chat-sidebar" wire:key="chat-sidebar">
            <div class="chat-sidebar-header">
                <h2 style="font-size: 1.5rem; margin: 0 0 0.75rem 0;">聊聊</h2>
                <div class="flex gap-2 mb-3">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchTerm"
                        class="flex-1 chat-sidebar-search"
                        placeholder="搜尋名稱或商品">
                </div>
            </div>

            <div class="chat-list">
                @forelse($this->conversations as $conversation)
                    @php
                        $otherUser = $conversation->getOtherUser(auth()->id());
                        $unreadCount = $conversation->getUnreadCount(auth()->id());
                    @endphp
                    <div
                        onclick="scrollToBottom()"
                        wire:click="selectConversation({{ $conversation->id }})"
                        wire:key="conversation-{{ $conversation->id }}"
                        class="chat-item {{ $selectedConversationId === $conversation->id ? 'active' : '' }}">
                        <div class="chat-item-avatar">
                            @if($otherUser->profile_photo_url && !str_contains($otherUser->profile_photo_url, 'ui-avatars.com'))
                                <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->last_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ $otherUser->last_name }}
                            @endif
                        </div>
                        <div class="chat-item-content">
                            <div class="chat-item-header">
                                <div class="chat-item-name">{{ $otherUser->last_name . $otherUser->first_name }}</div>
                                <div class="flex items-center gap-2">
                                    @if($unreadCount > 0)
                                        <span class="chat-item-badge">{{ $unreadCount }}</span>
                                    @endif
                                    <span class="chat-item-time">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="chat-item-message">
                                {{ $conversation->last_message ?? '尚無訊息' }}
                            </div>
                            <div class="chat-item-product">
                                <i class="mr-1 fas fa-box"></i>{{ $conversation->product->name ?? '' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: #ccc;">
                        <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p style="font-size: 0.9rem;">暫無對話</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 聊天主區域 -->
        <div class="chat-main" wire:key="chat-main">
            @if($this->selectedConversation)
                @php
                    $selectedConversation = $this->selectedConversation;
                    $otherUser = $selectedConversation->getOtherUser(auth()->id());
                    $isBuyer = $selectedConversation->buyer_id === auth()->id();
                @endphp

                <div style="display: flex; flex-direction: column; height: 100%;" wire:key="conversation-content-{{ $selectedConversation->id }}">
                    <!-- 聊天頭部 -->
                    <div style="border-bottom: 1px solid #e5e5ea; padding: 1rem; display: flex; justify-content: space-between; align-items: center; background: white;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="chat-item-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                @if($otherUser->profile_photo_url && !str_contains($otherUser->profile_photo_url, 'ui-avatars.com'))
                                    <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->last_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ $otherUser->last_name }}
                                @endif
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 0.95rem; font-weight: 600; color: #000;">
                                     {{ $otherUser->last_name }}
                                </h3>
                                <p style="margin: 0; font-size: 0.8rem; color: #666;">
                                    <i class="mr-1 fas fa-box"></i>{{ $selectedConversation->product->name ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <a
                                href="{{ route('products.show', $selectedConversation->product->slug ?? '') }}"
                                class="chat-header-action"
                                title="查看商品"
                                style="background: none; border: none; color: #0A84FF; cursor: pointer; font-size: 1.1rem; text-decoration: none;">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <button
                                wire:click="clearChat"
                                wire:confirm="確定要清除聊天記錄嗎？"
                                class="chat-header-action"
                                title="清除聊天記錄"
                                type="button"
                                style="background: none; border: none; color: #0A84FF; cursor: pointer; font-size: 1.1rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <!-- 🔥 商品資訊卡片 -->
                    <div style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); border-bottom: 1px solid #e5e5ea; padding: 1rem;">
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <!-- 商品圖片 -->
                            <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: white; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                @if($selectedConversation->product->images->isNotEmpty())
                                    @php
                                        $primaryImage = $selectedConversation->product->images->where('is_primary', true)->first();
                                        $image = $primaryImage ?? $selectedConversation->product->images->first();
                                    @endphp
                                    <img src="/storage/{{ $image->image_path }}" alt="{{ $selectedConversation->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="text-2xl text-gray-400 fas fa-image"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- 商品資訊 -->
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: #333;">
                                        {{ $selectedConversation->product->name ?? '' }}
                                    </h4>

                                    {{-- 🔥 歷史價格按鈕 --}}
                                    <button
                                        wire:click="togglePriceHistoryModal"
                                        type="button"
                                        style="padding: 0.4rem 0.75rem; background: #0A84FF; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.75rem; font-weight: 600; white-space: nowrap; box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);">
                                        <i class="mr-1 fas fa-chart-line"></i>歷史價格
                                    </button>
                                </div>

                                <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 0.5rem;">
                                    <div>
                                        <span style="font-size: 0.75rem; color: #666;">賣家</span>
                                        <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: #0A84FF;">
                                            {{ $selectedConversation->seller->last_name . $selectedConversation->seller->first_name }}
                                        </p>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: #666;">原價</span>
                                        <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: #999;">
                                            NT$ {{ number_format($selectedConversation->product->price ?? 0) }}
                                        </p>
                                    </div>
                                    @if($this->bestPrice['is_bargain'])
                                        <div>
                                            <span style="font-size: 0.75rem; color: #666;">
                                                <i class="mr-1 fas fa-handshake"></i>議價後
                                            </span>
                                            <p style="margin: 0; font-size: 0.9rem; font-weight: 700; color: #FF9500;">
                                                NT$ {{ number_format($this->bestPrice['price']) }} x {{ $this->bestPrice['quantity'] }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- 🔥 統一的加入購物車/成交按鈕 -->
                                @if($isBuyer)
                                    @if($isProductInCart)
                                        {{-- 已從此對話加入購物車 --}}
                                        <a href="{{ route('cart') }}" style="display: inline-block; padding: 0.6rem 1.5rem; background: linear-gradient(135deg, #34C759 0%, #2FA84A 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(52, 199, 89, 0.3);">
                                            <i class="mr-1 fas fa-check-circle"></i>
                                            已成交 - 前往購物車結帳
                                        </a>
                                    @else
                                        @if($this->bestPrice['is_bargain'])
                                            {{-- 議價商品：顯示成交確認按鈕 --}}
                                            <button
                                                wire:click="addProductToCart"
                                                wire:confirm="⚠️ 確認成交並加入購物車？

                    ⚠️ 注意：
                    點擊確認後，此商品將以議價加入購物車並視為成交，此議價將結束。
                    您可前往購物車完成結帳。

                    確定要繼續嗎？"
                                                type="button"
                                                style="padding: 0.6rem 1.5rem; background: linear-gradient(135deg, #FF9500 0%, #FF8C00 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(255, 149, 0, 0.3);">
                                                <i class="mr-1 fas fa-handshake"></i>
                                                確認成交並加入購物車
                                            </button>
                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.7rem; color: #666;">
                                                💡 議價成功！點擊按鈕確認成交並結束議價
                                            </p>
                                        @else
                                            {{-- 無議價：可以繼續議價或回商品頁以原價購買 --}}
                                            <a
                                                href="{{ route('products.show', $selectedConversation->product->slug) }}"
                                                style="display: inline-block; padding: 0.6rem 1.5rem; background: linear-gradient(135deg, #0A84FF 0%, #007AFF 100%); color: white; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(10, 132, 255, 0.3); text-decoration: none;">
                                                <i class="mr-1 fas fa-shopping-cart"></i>
                                                前往商品頁購買
                                            </a>
                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.7rem; color: #666;">
                                                💡 可返回商品頁以原價購買，或在下方開始議價
                                            </p>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- 訊息區域 -->
                    <div class="chat-messages" id="chatMessages" wire:key="messages-{{ $selectedConversation->id }}">
                        @foreach($this->messages as $message)
                            @if($message->type === 'system')
                                <!-- 系統訊息 -->
                                <div class="message-system" wire:key="message-{{ $message->id }}">
                                    <div class="message-system-content">
                                        {{ $message->content }}
                                    </div>
                                </div>
                            @elseif($message->isBargainMessage())
                                <!-- 🔥 議價訊息 -->
                                @php
                                    $bargain = \App\Models\BargainHistory::find($message->related_message_id);

                                    if ($message->type === 'bargain') {
                                        $unitPrice = $bargain->buyer_offer ?? $message->bargain_price;
                                        $quantity = $bargain->buyer_quantity ?? ($message->bargain_quantity ?? 1);
                                        $total = $bargain->buyer_total ?? ($unitPrice * $quantity);
                                    } elseif ($message->type === 'bargain_counter') {
                                        $unitPrice = $bargain->seller_offer ?? $message->bargain_price;
                                        $quantity = $bargain->seller_quantity ?? ($message->bargain_quantity ?? 1);
                                        $total = $bargain->seller_total ?? ($unitPrice * $quantity);
                                    } elseif ($message->type === 'bargain_accept' || $message->type === 'bargain_deal') {
                                        $unitPrice = $bargain->final_price ?? $message->bargain_price;
                                        $quantity = $bargain->final_quantity ?? ($message->bargain_quantity ?? 1);
                                        $total = $bargain->final_total ?? ($unitPrice * $quantity);
                                    } else {
                                        $unitPrice = $message->bargain_price;
                                        $quantity = $message->bargain_quantity ?? 1;
                                        $total = $unitPrice * $quantity;
                                    }
                                @endphp

                                <div class="message-group {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}" wire:key="message-{{ $message->id }}">
                                    @if($message->sender_id !== auth()->id())
                                        <div class="message-avatar">{{ $message->sender->last_name }}</div>
                                    @endif

                                    <div class="message-content bargain-message-content" style="max-width: 70%;">
                                        <div class="bargain-message">
                                            <div class="bargain-message-header">
                                                <i class="fas fa-handshake"></i>
                                                <span>
                                                    @switch($message->type)
                                                        @case('bargain')
                                                            {{ $isBuyer ? '您' : '買家' }}的議價
                                                            @break
                                                        @case('bargain_counter')
                                                            {{ $isBuyer ? '賣家' : '您' }}的反議價
                                                            @break
                                                        @case('bargain_accept')
                                                            {{ $isBuyer ? '賣家已接受' : '您已接受' }}
                                                            @break
                                                        @case('bargain_reject')
                                                            已拒絕議價
                                                            @break
                                                        @case('bargain_deal')
                                                            🎉 議價成交！
                                                            @break
                                                    @endswitch
                                                </span>
                                            </div>

                                            @if($unitPrice && $message->type !== 'bargain_reject')
                                                <div class="bargain-details">
                                                    <div class="bargain-detail-row">
                                                        <span class="bargain-detail-label">議價單價：</span>
                                                        <span class="bargain-detail-value">NT$ {{ number_format($unitPrice) }} / 個</span>
                                                    </div>
                                                    <div class="bargain-detail-row">
                                                        <span class="bargain-detail-label">購買數量：</span>
                                                        <span class="bargain-detail-value">{{ $quantity }} 個</span>
                                                    </div>
                                                    <div class="bargain-detail-row total">
                                                        <span class="bargain-detail-label">議價總額：</span>
                                                        <span class="bargain-detail-value">NT$ {{ number_format($total) }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <div style="font-size: 0.75rem; color: #999; margin-top: 0.5rem;">
                                                {{ $message->created_at->format('Y/m/d H:i') }}
                                            </div>

                                            {{-- 賣家收到買家的議價 --}}
                                            @if($this->shouldShowSellerActions($message))
                                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed #FF9500;">
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                                                        <button
                                                            wire:click="acceptBargain({{ $message->related_message_id }})"
                                                            type="button"
                                                            style="padding: 0.6rem 0.75rem; background-color: #34C759; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                                                            <i class="fas fa-check"></i> 接受議價
                                                        </button>
                                                        <button
                                                            wire:click="rejectBargain({{ $message->related_message_id }})"
                                                            type="button"
                                                            style="padding: 0.6rem 0.75rem; background-color: #FF3B30; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                                                            <i class="fas fa-times"></i> 拒絕
                                                        </button>
                                                    </div>

                                                    <div style="background: rgba(255, 255, 255, 0.7); padding: 0.75rem; border-radius: 8px;">
                                                        <p style="margin: 0 0 0.75rem 0; font-size: 0.85rem; color: #333; font-weight: 600;">
                                                            <i class="mr-1 fas fa-reply"></i>或提出反議價：
                                                        </p>

                                                        <div class="bargain-input-group">
                                                            <div class="bargain-input-wrapper">
                                                                <span class="bargain-input-prefix">NT$</span>
                                                                <input type="number" wire:model.live.debounce.300ms="counterPrice" placeholder="單價" class="bargain-input" min="1" step="1" style="padding-right: 3.5rem;">
                                                                <span class="bargain-input-suffix">/ 個</span>
                                                            </div>
                                                        </div>

                                                        <div class="bargain-input-group">
                                                            <div class="bargain-input-wrapper">
                                                                <span class="bargain-input-prefix">x</span>
                                                                <input type="number" wire:model.live.debounce.300ms="counterQuantity" placeholder="數量" class="bargain-input" min="1" max="{{ $selectedConversation->product->stock > 0 ? $selectedConversation->product->stock : 9999 }}" style="padding-right: 2.5rem;">
                                                                <span class="bargain-input-suffix">個</span>
                                                            </div>
                                                        </div>

                                                        @if($counterPrice && $counterQuantity)
                                                            <div style="background: #e6f3ff; padding: 0.5rem; border-radius: 6px; text-align: center; margin-bottom: 0.75rem;">
                                                                <span style="font-size: 0.75rem; color: #666;">反議價總額：</span>
                                                                <span style="font-size: 1.2rem; font-weight: 700; color: #0A84FF;">
                                                                    NT$ {{ number_format($this->counterTotal) }}
                                                                </span>
                                                            </div>
                                                        @endif

                                                        <button wire:click="counterBargain({{ $message->related_message_id }})" type="button" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #0A84FF 0%, #007AFF 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem;">
                                                            <i class="mr-2 fas fa-paper-plane"></i>送出反議價
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- 買家收到賣家的反議價 --}}
                                            @if($this->shouldShowBuyerActions($message))
                                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed #FF9500;">
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                        <button wire:click="confirmDeal({{ $message->related_message_id }})" type="button" style="padding: 0.6rem 0.75rem; background-color: #34C759; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                                                            <i class="fas fa-handshake"></i> 確認成交
                                                        </button>
                                                        <button wire:click="toggleBargainPanel" type="button" style="padding: 0.6rem 0.75rem; background-color: #FF9500; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                                                            <i class="fas fa-comment-dollar"></i> 繼續議價
                                                        </button>
                                                    </div>
                                                    <p style="margin: 0; font-size: 0.7rem; color: #666; text-align: center; padding: 0.4rem; background: rgba(255, 255, 255, 0.5); border-radius: 4px;">
                                                        💡 同意成交或繼續議價提出新價格
                                                    </p>
                                                </div>
                                            @endif

                                            {{-- 賣家接受議價後 --}}
                                            @if($message->type === 'bargain_accept')
                                                @php
                                                    $bargain = \App\Models\BargainHistory::find($message->related_message_id);
                                                @endphp
                                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed #34C759;">
                                                    <div style="padding: 1rem; background: rgba(52, 199, 89, 0.15); border-radius: 10px; text-align: center; border: 1px solid #34C759;">
                                                        <p style="margin: 0; font-size: 0.9rem; color: #34C759; font-weight: 600;">
                                                            <i class="mr-1 fas fa-check-circle"></i>
                                                            @if($isBuyer)
                                                                賣家已接受您的議價！
                                                            @else
                                                                已接受買家議價
                                                            @endif
                                                        </p>

                                                        {{-- 🔥 顯示成交狀態 --}}
                                                        @if($bargain && $bargain->status === 'completed')
                                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #34C759; font-weight: 600;">
                                                                ✅ 買家已確認成交
                                                            </p>
                                                        @else
                                                            @if($isBuyer)
                                                                <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #666;">
                                                                    💡 請至頂部點擊「確認成交」按鈕
                                                                </p>
                                                            @else
                                                                <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #666;">
                                                                    等待買家確認成交
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- 成交後 --}}
                                            @if($message->type === 'bargain_deal')
                                                @php
                                                    $bargain = \App\Models\BargainHistory::find($message->related_message_id);
                                                @endphp
                                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed #34C759;">
                                                    <div style="padding: 1rem; background: rgba(52, 199, 89, 0.15); border-radius: 10px; text-align: center; border: 1px solid #34C759;">
                                                        <p style="margin: 0; font-size: 0.9rem; color: #34C759; font-weight: 600;">
                                                            <i class="mr-1 fas fa-check-circle"></i> 雙方議價成功！
                                                        </p>

                                                        {{-- 🔥 顯示成交狀態 --}}
                                                        @if($bargain && $bargain->status === 'completed')
                                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #34C759; font-weight: 600;">
                                                                ✅ 買家已確認成交
                                                            </p>
                                                        @else
                                                            @if($isBuyer)
                                                                <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #666;">
                                                                    💡 請至頂部點擊「確認成交」按鈕
                                                                </p>
                                                            @else
                                                                <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #666;">
                                                                    等待買家確認成交
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($message->sender_id === auth()->id())
                                        <div class="message-avatar">{{ $message->sender->last_name }}</div>
                                    @endif
                                </div>

                            @elseif($message->type === 'image')
                                <!-- 圖片訊息 -->
                                <div class="message-group {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}" wire:key="message-{{ $message->id }}">
                                    @if($message->sender_id !== auth()->id())
                                        <div class="message-avatar">{{ $message->sender->last_name }}</div>
                                    @endif
                                    <div class="message-content">
                                        <div style="max-width: 300px; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                            <img src="{{ Storage::url($message->image_path) }}" alt="Image" style="width: 100%; display: block; cursor: pointer;" onclick="window.open('{{ Storage::url($message->image_path) }}', '_blank')">
                                        </div>
                                        <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                                    </div>
                                    @if($message->sender_id === auth()->id())
                                        <div class="message-avatar">{{ $message->sender->last_name }}</div>
                                    @endif
                                </div>

                            @else
                                <!-- 一般文字訊息 -->
                                <div class="message-group {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}" wire:key="message-{{ $message->id }}">
                                    @if($message->sender_id !== auth()->id())
                                        <div class="message-avatar">{{ $message->sender->last_name }}</div>
                                    @endif
                                    <div class="message-content">
                                        <div class="message-bubble">{{ $message->content }}</div>
                                        <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                                    </div>
                                    @if($message->sender_id === auth()->id())
                                        <div class="message-avatar">{{ $message->sender->last_name }}</div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- 🔥 議價面板 -->
                    @if($showBargainPanel)
                        <div style="border-top: 1px solid #e5e5ea; padding: 1rem; background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 600; color: #0A84FF;">
                                    <i class="fas fa-handshake" style="margin-right: 0.4rem;"></i>議價模式
                                </h4>
                                <button wire:click="toggleBargainPanel" type="button" style="background: none; border: none; color: #999; cursor: pointer; font-size: 1.1rem;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            @if($this->bargainStats && $this->bargainStats->min_price)
                                <div style="background: white; padding: 0.75rem; border-radius: 8px; margin-bottom: 0.75rem; border: 1px solid #d4e6ff;">
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.75rem; color: #666; font-weight: 600;">
                                        <i class="mr-1 fas fa-chart-line"></i>📊 歷史成交區間：
                                    </p>
                                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                                        <div style="flex: 1; text-align: center;">
                                            <span style="font-size: 0.7rem; color: #999;">最低價</span>
                                            <p style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #34C759;">
                                                NT$ {{ number_format($this->bargainStats->min_price) }}
                                            </p>
                                        </div>
                                        <div style="flex: 2; height: 3px; background: linear-gradient(90deg, #34C759, #0A84FF); border-radius: 2px;"></div>
                                        <div style="flex: 1; text-align: center;">
                                            <span style="font-size: 0.7rem; color: #999;">最高價</span>
                                            <p style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0A84FF;">
                                                NT$ {{ number_format($this->bargainStats->max_price) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($isBuyer)
                                @if(!$this->currentBargain || $this->currentBargain->status === 'rejected')
                                    <div style="background: white; padding: 1rem; border-radius: 8px; border: 2px solid #0A84FF;">
                                        <p style="margin: 0 0 0.75rem 0; font-size: 0.9rem; color: #333; font-weight: 600;">
                                            <i class="fas fa-tag" style="margin-right: 0.4rem; color: #0A84FF;"></i>開始議價
                                        </p>

                                        <div style="margin-bottom: 0.6rem;">
                                            <label style="display: block; font-size: 0.75rem; color: #666; margin-bottom: 0.3rem; font-weight: 500;">議價單價</label>
                                            <div class="bargain-input-wrapper">
                                                <span class="bargain-input-prefix">NT$</span>
                                                <input type="number" wire:model.live.debounce.300ms="bargainPrice" placeholder="  輸入您想要的單價" class="bargain-input" min="1" step="1" style="padding: 0.6rem 0.6rem 0.6rem 2.2rem; font-size: 0.85rem;">
                                                <span class="bargain-input-suffix">/ 個</span>
                                            </div>
                                        </div>

                                        <div style="margin-bottom: 0.6rem;">
                                            <label style="display: block; font-size: 0.75rem; color: #666; margin-bottom: 0.3rem; font-weight: 500;">購買數量</label>
                                            <div class="bargain-input-wrapper">
                                                <span class="bargain-input-prefix">x</span>
                                                <input type="number" wire:model.live.debounce.300ms="bargainQuantity" placeholder="輸入購買數量" class="bargain-input" min="1" max="{{ $selectedConversation->product->stock > 0 ? $selectedConversation->product->stock : 9999 }}" style="padding: 0.6rem 0.6rem 0.6rem 2.2rem; font-size: 0.85rem;">
                                                <span class="bargain-input-suffix">個</span>
                                            </div>
                                            @if($selectedConversation->product->stock > 0)
                                                <p style="margin: 0.3rem 0 0 0; font-size: 0.7rem; color: #999;">庫存：{{ $selectedConversation->product->stock }} 個</p>
                                            @endif
                                        </div>

                                        @if($bargainPrice && $bargainQuantity)
                                            <div style="background: linear-gradient(135deg, #e6f3ff 0%, #cce7ff 100%); border: 2px solid #0A84FF; border-radius: 8px; padding: 0.75rem; text-align: center; margin: 0.6rem 0;">
                                                <div style="font-size: 0.75rem; color: #666; margin-bottom: 0.2rem;">議價總額</div>
                                                <div style="font-size: 1.5rem; font-weight: 700; color: #0A84FF;">NT$ {{ number_format($this->bargainTotal) }}</div>
                                            </div>
                                        @endif

                                        <button wire:click="submitBargain" type="button" @if(!$bargainPrice || !$bargainQuantity) disabled @endif style="width: 100%; padding: 0.75rem 1rem; background: linear-gradient(135deg, #0A84FF 0%, #007AFF 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; box-shadow: 0 3px 10px rgba(10, 132, 255, 0.3);">
                                            <i class="fas fa-paper-plane" style="margin-right: 0.4rem;"></i>送出議價
                                        </button>

                                        <div style="background: #f9f9f9; padding: 0.6rem; border-radius: 6px; margin-top: 0.6rem;">
                                            <p style="margin: 0; font-size: 0.7rem; color: #666;">💡 提示：議價後賣家可以選擇接受、拒絕或提出反議價</p>
                                        </div>
                                    </div>
                                @else
                                    <div style="text-align: center; padding: 2rem 1rem; background: white; border-radius: 8px;">
                                        <i class="fas fa-hourglass-half" style="font-size: 2.5rem; margin-bottom: 0.6rem; color: #0A84FF;"></i>
                                        <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #0A84FF;">議價進行中...</p>
                                        <p style="margin: 0.4rem 0 0 0; font-size: 0.8rem; color: #666;">請在訊息中查看賣家的回覆</p>
                                    </div>
                                @endif
                            @else
                                <div style="text-align: center; padding: 2rem 1rem; background: white; border-radius: 8px;">
                                    <i class="fas fa-info-circle" style="font-size: 2.5rem; margin-bottom: 0.6rem; color: #999;"></i>
                                    <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #666;">等待買家發起議價</p>
                                    <p style="margin: 0.4rem 0 0 0; font-size: 0.8rem; color: #999;">收到議價時可直接在訊息中回覆</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- 輸入框 -->
                    <div style="border-top: 1px solid #e5e5ea; padding: 1rem; background: white;">
                        @if($uploadedImage)
                            <div style="padding: 0.75rem; background: #f9f9f9; border-radius: 8px; margin-bottom: 0.75rem; position: relative;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 60px; height: 60px; border-radius: 6px; overflow: hidden; background: white;">
                                        <img src="{{ $uploadedImage->temporaryUrl() }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div style="flex: 1;">
                                        <p style="margin: 0; font-size: 0.85rem; color: #333; font-weight: 600;">已選擇圖片</p>
                                        <p style="margin: 0; font-size: 0.75rem; color: #999;">{{ $uploadedImage->getClientOriginalName() }}</p>
                                    </div>
                                    <button wire:click="$set('uploadedImage', null)" type="button" style="padding: 0.5rem; background: #FF3B30; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button wire:click="sendImage" type="button" style="padding: 0.5rem 1rem; background: #0A84FF; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                        <i class="fas fa-paper-plane"></i> 發送
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                            <div style="flex: 1; display: flex; gap: 0.5rem; align-items: flex-end;">
                                <textarea wire:model.live.debounce.300ms="messageContent" wire:keydown.enter.prevent="sendMessage" class="chat-input" placeholder="輸入訊息..." rows="1" @if($uploadedImage) disabled @endif></textarea>
                            </div>
                            <div style="display: flex; gap: 0.25rem;">
                                <label for="imageUpload-{{ $selectedConversation->id }}" style="width: 36px; height: 36px; border-radius: 50%; background: none; border: 1px solid #e5e5ea; color: #0A84FF; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;" title="上傳圖片">
                                    <i class="fas fa-image"></i>
                                </label>
                                <input type="file" id="imageUpload-{{ $selectedConversation->id }}" wire:model="uploadedImage" accept="image/*" style="display: none;">

                                <button wire:click="toggleBargainPanel" type="button" @if($uploadedImage) disabled @endif style="width: 36px; height: 36px; border-radius: 50%; background: {{ $showBargainPanel ? '#0A84FF' : 'none' }}; border: {{ $showBargainPanel ? 'none' : '1px solid #e5e5ea' }}; color: {{ $showBargainPanel ? 'white' : '#0A84FF' }}; cursor: pointer; font-size: 1.1rem;" title="議價模式">
                                    <i class="fas fa-handshake"></i>
                                </button>
                            </div>
                            <button wire:click="sendMessage" type="button" @if($uploadedImage) disabled @endif style="width: 36px; height: 36px; border-radius: 50%; background-color: #0A84FF; border: none; color: white; cursor: pointer; font-size: 1rem;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>

                        <div wire:loading wire:target="uploadedImage" style="margin-top: 0.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #0A84FF; font-size: 0.85rem;">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>正在上傳圖片...</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full gap-4 text-gray-400">
                    <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #e8f4f8 0%, #f0f8fc 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-comments" style="font-size: 3rem; color: #0A84FF; opacity: 0.3;"></i>
                    </div>
                    <h3 style="color: #333; font-size: 1.1rem; margin: 0;">歡迎使用聊聊功能</h3>
                    <p style="color: #999; font-size: 0.95rem; margin: 0;">選擇一個對話開始聊天</p>
                </div>
            @endif
        </div>
    </div>

    <div wire:loading.flex wire:target="selectedConversation" style="width:100%;height:100%;position:fixed;top:0;left:0;z-index:9999;align-items:center;justify-content:center;background-color:rgba(0, 0, 0, 0.5);">
        <div class="flex flex-col items-center justify-center p-6 bg-white rounded-lg">
            <div class="mx-auto">
                <img src="{{ asset('images/loading.gif') }}" width="150" />
            </div>
            <p class="mt-4 text-gray-600">載入中...</p>
        </div>
    </div>
    {{-- 🔥 歷史價格彈窗 --}}
    @if($showPriceHistoryModal)
        <div
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div
                style="background: white; border-radius: 16px; max-width: 600px; width: 100%; max-height: 80vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column;">
                {{-- 標題 --}}
                <div style="padding: 1.5rem; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #027CFF">
                    <h3 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: white; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-chart-line"></i>
                        歷史成交價格
                    </h3>
                    <button
                        wire:click="togglePriceHistoryModal"
                        type="button"
                        style="background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- 統計資訊 --}}
                @if($this->priceStats)
                    <div style="padding: 1rem; background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); border-bottom: 1px solid #e5e5ea;">
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                            <div style="text-align: center; padding: 0.75rem; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="font-size: 0.7rem; color: #666; margin-bottom: 0.25rem;">成交筆數</div>
                                <div style="font-size: 1.3rem; font-weight: 700; color: #667eea;">{{ $this->priceStats['count'] }}</div>
                            </div>
                            <div style="text-align: center; padding: 0.75rem; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="font-size: 0.7rem; color: #666; margin-bottom: 0.25rem;">最低價</div>
                                <div style="font-size: 1.1rem; font-weight: 700; color: #34C759;">NT$ {{ number_format($this->priceStats['min']) }}</div>
                            </div>
                            <div style="text-align: center; padding: 0.75rem; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="font-size: 0.7rem; color: #666; margin-bottom: 0.25rem;">最高價</div>
                                <div style="font-size: 1.1rem; font-weight: 700; color: #FF3B30;">NT$ {{ number_format($this->priceStats['max']) }}</div>
                            </div>
                            <div style="text-align: center; padding: 0.75rem; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="font-size: 0.7rem; color: #666; margin-bottom: 0.25rem;">平均價</div>
                                <div style="font-size: 1.1rem; font-weight: 700; color: #0A84FF;">NT$ {{ number_format($this->priceStats['avg']) }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 歷史記錄列表 --}}
                <div style="flex: 1; overflow-y: auto; padding: 1rem;">
                    @if($this->priceHistory && $this->priceHistory->isNotEmpty())
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @foreach($this->priceHistory as $index => $item)
                                <div style="background: {{ $item['is_bargain'] ? 'linear-gradient(135deg, #fff5e6 0%, #ffe6cc 100%)' : '#f9f9f9' }}; padding: 1rem; border-radius: 10px; border-left: 4px solid {{ $item['is_bargain'] ? '#FF9500' : '#0A84FF' }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="background: {{ $item['is_bargain'] ? '#FF9500' : '#0A84FF' }}; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                                {{ $index + 1 }}
                                            </span>
                                            @if($item['is_bargain'])
                                                <span style="background: #FF9500; color: white; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                                                    <i class="mr-1 fas fa-handshake"></i>議價
                                                </span>
                                            @else
                                                <span style="background: #0A84FF; color: white; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                                                    <i class="mr-1 fas fa-tag"></i>原價
                                                </span>
                                            @endif
                                        </div>
                                        <span style="font-size: 0.75rem; color: #999;">
                                            <i class="mr-1 far fa-clock"></i>{{ $item['date']->diffForHumans() }}
                                        </span>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem;">單價 x 數量</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #333;">
                                                NT$ {{ number_format($item['price']) }} x {{ $item['quantity'] }}
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem;">總價</div>
                                            <div style="font-size: 1.1rem; font-weight: 700; color: {{ $item['is_bargain'] ? '#FF9500' : '#0A84FF' }};">
                                                NT$ {{ number_format($item['total']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 3rem 1rem; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p style="font-size: 0.9rem; margin: 0;">暫無歷史成交記錄</p>
                            <p style="font-size: 0.8rem; margin: 0.5rem 0 0 0;">成為第一個購買的人吧！</p>
                        </div>
                    @endif
                </div>

                {{-- 底部說明 --}}
                <div style="padding: 1rem; background: #f9f9f9; border-top: 1px solid #e5e5ea;">
                    <p style="margin: 0; font-size: 0.75rem; color: #666; text-align: center;">
                        <i class="mr-1 fas fa-info-circle"></i>
                        顯示最近 10 筆已完成的訂單成交價格供參考
                    </p>
                </div>
            </div>
        </div>
    @endif
    <script>
        function scrollToBottom() {
            try {
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    const forceScroll = () => {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    };
                    const scrollToLastMessage = () => {
                        const messages = chatMessages.querySelectorAll('.message-group, .message-system, .bargain-message');
                        if (messages.length > 0) {
                            messages[messages.length - 1].scrollIntoView({ behavior: 'auto', block: 'end' });
                        }
                    };
                    forceScroll();
                    scrollToLastMessage();
                    requestAnimationFrame(() => {
                        forceScroll();
                        scrollToLastMessage();
                        setTimeout(() => { forceScroll(); scrollToLastMessage(); }, 50);
                        setTimeout(() => { forceScroll(); scrollToLastMessage(); }, 150);
                        setTimeout(() => { forceScroll(); scrollToLastMessage(); }, 300);
                    });
                }
            } catch (error) {
                console.error('Scroll error:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(scrollToBottom, 100);
            setTimeout(scrollToBottom, 500);
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', () => {
                scrollToBottom();
            });
        });
    </script>
</div>

@push('scripts')
<script>
    console.log('=== 🚀 Script Loading ===');

    function waitForLivewire(callback) {
        if (typeof window.Livewire !== 'undefined') {
            console.log('✅ Livewire is ready');
            callback();
        } else {
            console.log('⏳ Waiting for Livewire...');
            setTimeout(() => waitForLivewire(callback), 100);
        }
    }

    waitForLivewire(() => {
        console.log('=== 🎬 Starting initialization ===');

        setTimeout(scrollToBottom, 100);
        setTimeout(scrollToBottom, 500);
        setTimeout(scrollToBottom, 1000);

        Livewire.on('conversation-selected', () => {
            console.log('📢 Event: conversation-selected');
            setTimeout(scrollToBottom, 100);
            setTimeout(scrollToBottom, 300);
            setTimeout(scrollToBottom, 500);
        });

        Livewire.on('message-sent', () => {
            console.log('📢 Event: message-sent');
            setTimeout(scrollToBottom, 50);
            setTimeout(scrollToBottom, 200);
            setTimeout(scrollToBottom, 400);
        });

        Livewire.on('message-received', () => {
            console.log('📢 Event: message-received');
            setTimeout(scrollToBottom, 50);
            setTimeout(scrollToBottom, 200);
            setTimeout(scrollToBottom, 400);
        });

        Livewire.hook('morph.updated', ({ el, component }) => {
            scrollToBottom();
            setTimeout(scrollToBottom, 100);
        });

        console.log('=== 🌐 Setting up WebSocket ===');

        const userId = {{ auth()->id() }};
        console.log('👤 Current User ID:', userId);

        let currentConversationId = @js($selectedConversationId);
        console.log('💬 Current Conversation ID:', currentConversationId);

        let conversationChannel = null;
        let userChannel = null;

        function callLivewireMethod(method, ...params) {
            const component = Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );
            return component.call(method, ...params);
        }

        function getLivewireProperty(property) {
            const component = Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );
            return component.get(property);
        }

        function waitForEcho(callback) {
            if (typeof window.Echo !== 'undefined' && window.Echo.connector) {
                console.log('✅ Echo is ready');
                callback();
            } else {
                console.log('⏳ Waiting for Echo...');
                setTimeout(() => waitForEcho(callback), 100);
            }
        }

        waitForEcho(() => {
            console.log('=== 🎧 Starting WebSocket Listeners ===');

            console.log('🔌 Socket ID:', window.Echo.socketId());
            console.log('🔌 Connection state:', window.Echo.connector.pusher.connection.state);

            try {
                console.log('📡 Subscribing to user channel: user.' + userId);

                userChannel = window.Echo.private(`user.${userId}`)
                    .subscribed(() => {
                        console.log('✅ [User Channel] Successfully subscribed to: user.' + userId);
                    })
                    .listen('.conversation.updated', (e) => {
                        console.log('📨 [User Channel] Event: conversation.updated');
                        console.log('🔄 [User Channel] Calling refreshConversations...');
                        try {
                            callLivewireMethod('refreshConversations');
                        } catch (error) {
                            console.error('❌ Failed to call refreshConversations:', error);
                        }
                    })
                    .error((error) => {
                        console.error('❌ [User Channel] Subscription error:', error);
                    });
            } catch (error) {
                console.error('❌ Failed to subscribe to user channel:', error);
            }

            function subscribeToConversation(conversationId) {
                if (!conversationId) {
                    console.warn('⚠️ subscribeToConversation called with no conversationId');
                    return;
                }

                console.log('=== 📡 Subscribing to Conversation ===');
                console.log('Conversation ID:', conversationId);

                if (conversationChannel && currentConversationId) {
                    const oldChannelName = `private-conversation.${currentConversationId}`;
                    console.log('❌ Leaving old channel:', oldChannelName);
                    window.Echo.leave(oldChannelName);
                    conversationChannel = null;
                }

                const channelName = `conversation.${conversationId}`;
                console.log('📡 Attempting to subscribe to:', channelName);

                try {
                    conversationChannel = window.Echo.private(channelName)
                        .subscribed(() => {
                            console.log('✅ Successfully subscribed to:', channelName);
                        })
                        .listen('.message.sent', (e) => {
                            console.log('📨 MESSAGE RECEIVED on', channelName);
                            console.log('🔄 Calling refreshMessages...');
                            try {
                                callLivewireMethod('refreshMessages');
                                Livewire.dispatch('message-received');
                            } catch (error) {
                                console.error('❌ Failed to call refreshMessages:', error);
                            }
                            playNotificationSound();
                        })
                        .error((error) => {
                            console.error('❌ Channel error:', error);
                        });

                    currentConversationId = conversationId;
                    console.log('✅ Channel object created');

                } catch (error) {
                    console.error('❌ Exception while subscribing:', error);
                }
            }

            if (currentConversationId) {
                console.log('🎬 Initial subscription on page load');
                setTimeout(() => {
                    subscribeToConversation(currentConversationId);
                }, 500);
            }

            Livewire.on('conversation-selected', () => {
                console.log('=== 🔔 Conversation Selected Event ===');
                setTimeout(() => {
                    try {
                        const newConversationId = getLivewireProperty('selectedConversationId');
                        console.log('New conversation ID:', newConversationId);

                        if (newConversationId && newConversationId !== currentConversationId) {
                            console.log('🔀 Switching to new conversation');
                            subscribeToConversation(newConversationId);
                        }
                    } catch (error) {
                        console.error('❌ Failed to get conversation ID:', error);
                    }
                }, 200);
            });

            function playNotificationSound() {
                try {
                    const audio = new Audio('/sounds/notification.mp3');
                    audio.volume = 0.3;
                    audio.play().catch(e => console.log('🔇 Cannot play sound'));
                } catch (e) {
                    console.log('🔇 Sound not available');
                }
            }
        });

        Livewire.on('notify', (event) => {
            const data = event[0];
            const toast = document.createElement('div');

            let bgColor = 'bg-blue-500';
            if (data.type === 'success') bgColor = 'bg-green-500';
            if (data.type === 'error') bgColor = 'bg-red-500';
            if (data.type === 'warning') bgColor = 'bg-yellow-500';

            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            toast.innerHTML = `<i class="fas fa-${data.type === 'success' ? 'check' : 'info'}-circle mr-2"></i>${data.message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });

        console.log('=== ✅ Initialization Complete ===');
    });
</script>
@endpush
