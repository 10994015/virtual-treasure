<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\BargainHistory;
use App\Events\NewMessageEvent;
use App\Events\ConversationUpdated;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessagingComponent extends Component
{
    use WithFileUploads;

    public ?int $conversationId = null;

    public $selectedConversationId = null;
    public $messageContent = '';
    public $searchTerm = '';

    // 🔥 議價相關（支援數量）
    public $showBargainPanel = false;
    public $bargainPrice = null;
    public $bargainQuantity = 1;  // 🔥 新增：議價數量

    // 🔥 反議價相關
    public $counterPrice = null;
    public $counterQuantity = null;

    // 圖片上傳
    public $uploadedImage = null;

    public $isProductInCart = false;
    public $cartItemType = null; // 'original' 或 'bargain'

    public function mount($conversationId = null)
    {
         if ($conversationId) {
            $this->selectConversation($conversationId);
        }

    }
    protected function checkProductInCart()
    {
        if (!$this->selectedConversationId) {
            $this->isProductInCart = false;
            $this->cartItemType = null;
            return;
        }

        $conversation = $this->selectedConversation;
        if (!$conversation) {
            $this->isProductInCart = false;
            $this->cartItemType = null;
            return;
        }

        $cart = [];
        $cartCookie = request()->cookie('shopping_cart');
        if ($cartCookie) {
            $cart = json_decode($cartCookie, true) ?? [];
        }

        // 🔥 關鍵：只檢查「從這個對話」加入購物車的商品
        foreach ($cart as $item) {
            if (isset($item['conversation_id']) && $item['conversation_id'] == $this->selectedConversationId) {
                $this->isProductInCart = true;
                $this->cartItemType = isset($item['is_bargain']) && $item['is_bargain'] ? 'bargain' : 'original';
                return;
            }
        }

        // 🔥 沒有找到從此對話加入的商品
        $this->isProductInCart = false;
        $this->cartItemType = null;
    }


    public function getBestPriceProperty()
    {
        if (!$this->selectedConversation) {
            return null;
        }

        $product = $this->selectedConversation->product;

        // 🔥 查找此對話中「未加入購物車」的最新成交或接受的議價
        $latestDeal = BargainHistory::where('conversation_id', $this->selectedConversationId)
            ->whereIn('status', ['deal', 'accepted'])
            ->whereNull('added_to_cart_at') // 🔥 關鍵：排除已加入購物車的
            ->latest()
            ->first();

        if ($latestDeal && $latestDeal->final_price && $latestDeal->final_quantity) {
            return [
                'price' => $latestDeal->final_price,
                'quantity' => $latestDeal->final_quantity,
                'is_bargain' => true,
                'bargain_id' => $latestDeal->id,
            ];
        }

        return [
            'price' => $product->price,
            'quantity' => 1,
            'is_bargain' => false,
            'bargain_id' => null,
        ];
    }

    // 🔥 新增：統一的加入購物車方法
    public function addProductToCart()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        try {
            $conversation = Conversation::with('product.images')->findOrFail($this->selectedConversationId);
            $product = $conversation->product;

            // 🔥 檢查是否已從此對話加入購物車
            if ($this->isProductInCart) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => '此對話的商品已在購物車中'
                ]);
                return redirect()->route('cart');
            }

            $bestPrice = $this->bestPrice;

            // 檢查庫存
            if ($product->stock > 0 && $bestPrice['quantity'] > $product->stock) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "數量超過庫存！目前庫存：{$product->stock}"
                ]);
                return;
            }

            DB::beginTransaction();

            $cart = [];
            $cartCookie = request()->cookie('shopping_cart');
            if ($cartCookie) {
                $cart = json_decode($cartCookie, true) ?? [];
            }

            // 取得商品圖片
            $image = null;
            if ($product->images->isNotEmpty()) {
                $primaryImage = $product->images->where('is_primary', true)->first();
                $image = $primaryImage ? $primaryImage->image_path : $product->images->first()->image_path;
            }

            // 🔥 建立購物車項目（使用 conversation_id 作為唯一標識）
            $cartItem = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $bestPrice['price'],
                'quantity' => $bestPrice['quantity'],
                'image' =>  '/storage/'.$image,
                'stock' => $product->stock,
                'game_type' => $product->game_type,
                'category' => $product->category,
                'conversation_id' => $this->selectedConversationId, // 🔥 關鍵：綁定對話ID
            ];

            // 🔥 如果是議價商品，標記相關資訊
            if ($bestPrice['is_bargain']) {
                $cartItem['is_bargain'] = true;
                $cartItem['bargain_id'] = $bestPrice['bargain_id'];
                $cartItem['locked_quantity'] = true;
                $cartItem['locked_price'] = true;

                // 🔥 標記議價已加入購物車（成交）
                $bargain = BargainHistory::find($bestPrice['bargain_id']);
                if ($bargain) {
                    $bargain->update([
                        'added_to_cart_at' => now(),
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    // 🔥 發送系統訊息通知成交
                    Message::create([
                        'conversation_id' => $this->selectedConversationId,
                        'sender_id' => auth()->id(),
                        'type' => 'system',
                        'content' => sprintf(
                            '✅ 買家已確認成交並加入購物車！成交價：NT$ %s x %d = NT$ %s。議價已結束。',
                            number_format($bestPrice['price']),
                            $bestPrice['quantity'],
                            number_format($bestPrice['price'] * $bestPrice['quantity'])
                        ),
                    ]);

                    // 更新對話最後訊息
                    $conversation->updateLastMessage('買家已確認成交', auth()->id());

                    // 廣播訊息更新
                    broadcast(new ConversationUpdated($conversation));
                }
            }

            $cart[] = $cartItem;

            cookie()->queue('shopping_cart', json_encode($cart), 43200);

            DB::commit();

            $this->isProductInCart = true;
            $this->cartItemType = $bestPrice['is_bargain'] ? 'bargain' : 'original';

            $message = $bestPrice['is_bargain']
                ? sprintf('已確認成交並加入購物車！議價：%d 個 x NT$ %s = NT$ %s',
                    $bestPrice['quantity'],
                    number_format($bestPrice['price']),
                    number_format($bestPrice['price'] * $bestPrice['quantity']))
                : sprintf('已加入購物車：%d 個 x NT$ %s',
                    $bestPrice['quantity'],
                    number_format($bestPrice['price']));

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);

            $this->dispatch('cart-updated', ['count' => count($cart)]);
            $this->dispatch('message-sent');

            return redirect()->route('cart');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add product to cart error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '加入購物車失敗：' . $e->getMessage()
            ]);
        }
    }





    #[Computed]
    public function conversations()
    {
        try {
            $query = Conversation::query()
                ->where(function($q) {
                    $q->where('buyer_id', auth()->id())
                      ->orWhere('seller_id', auth()->id());
                });

            if ($this->searchTerm) {
                $query->where(function($q) {
                    $q->whereHas('buyer', function($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    })->orWhereHas('seller', function($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    })->orWhereHas('product', function($productQuery) {
                        $productQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
                });
            }

            return $query->with(['buyer', 'seller', 'product.images'])
                        ->latest('last_message_at')
                        ->get();

        } catch (\Exception $e) {
            Log::error('Load conversations error: ' . $e->getMessage());
            return collect([]);
        }
    }

    #[Computed]
    public function selectedConversation()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return Conversation::with(['buyer', 'seller', 'product.images'])
                          ->find($this->selectedConversationId);
    }

    #[Computed]
    public function messages()
    {
        if (!$this->selectedConversationId) {
            return collect([]);
        }

        try {
            return Message::where('conversation_id', $this->selectedConversationId)
                         ->with('sender')
                         ->orderBy('created_at', 'asc')
                         ->get();
        } catch (\Exception $e) {
            Log::error('Load messages error: ' . $e->getMessage());
            return collect([]);
        }
    }

    #[Computed]
    public function currentBargain()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return BargainHistory::where('conversation_id', $this->selectedConversationId)
            ->whereIn('status', ['pending', 'countered', 'deal'])
            ->latest()
            ->first();
    }

    #[Computed]
    public function bargainStats()
    {
        if (!$this->selectedConversation) {
            return null;
        }

        return BargainHistory::where('product_id', $this->selectedConversation->product_id)
            ->where('status', 'deal')
            ->selectRaw('MIN(final_price) as min_price, MAX(final_price) as max_price, AVG(final_price) as avg_price')
            ->first();
    }

    // 🔥 新增：計算議價總價
    public function getBargainTotalProperty()
    {
        if (!$this->bargainPrice || !$this->bargainQuantity) {
            return 0;
        }
        return $this->bargainPrice * $this->bargainQuantity;
    }

    // 🔥 新增：計算反議價總價
    public function getCounterTotalProperty()
    {
        if (!$this->counterPrice || !$this->counterQuantity) {
            return 0;
        }
        return $this->counterPrice * $this->counterQuantity;
    }

    public function isLatestPendingBargain($message)
    {
        $latestBargain = $this->currentBargain;

        if (!$latestBargain) {
            return false;
        }

        return $message->related_message_id === $latestBargain->id;
    }

    public function shouldShowSellerActions($message)
    {
        if (!$this->selectedConversation) {
            return false;
        }

        $isSeller = $this->selectedConversation->seller_id === auth()->id();
        if (!$isSeller) {
            return false;
        }

        if ($message->type !== 'bargain') {
            return false;
        }

        if (!$this->isLatestPendingBargain($message)) {
            return false;
        }

        if ($message->sender_id === auth()->id()) {
            return false;
        }

        $latestBargain = $this->currentBargain;
        return $latestBargain && $latestBargain->status === 'pending';
    }

    public function shouldShowBuyerActions($message)
    {
        if (!$this->selectedConversation) {
            return false;
        }

        $isBuyer = $this->selectedConversation->buyer_id === auth()->id();
        if (!$isBuyer) {
            return false;
        }

        if ($message->type !== 'bargain_counter') {
            return false;
        }

        if (!$this->isLatestPendingBargain($message)) {
            return false;
        }

        if ($message->sender_id === auth()->id()) {
            return false;
        }

        $latestBargain = $this->currentBargain;
        return $latestBargain && $latestBargain->status === 'countered';
    }

    public function selectConversation($conversationId)
    {
        try {
            $conversation = Conversation::with(['buyer', 'seller'])->findOrFail($conversationId);

            if ($conversation->buyer_id !== auth()->id() && $conversation->seller_id !== auth()->id()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => '您沒有權限查看此對話'
                ]);
                return;
            }

            $this->selectedConversationId = $conversationId;

            // 🔥 更新瀏覽器 URL（不重新載入頁面）
            $this->js("window.history.pushState({}, '', '/messages/{$conversationId}')");

            $conversation->markAsRead(auth()->id());

            // 重置議價表單
            $this->showBargainPanel = false;
            $this->bargainPrice = null;
            $this->bargainQuantity = 1;
            $this->counterPrice = null;
            $this->counterQuantity = null;
            $this->uploadedImage = null;

            // 🔥 檢查購物車狀態
            $this->checkProductInCart();

            $this->dispatch('conversation-selected');

        } catch (\Exception $e) {
            Log::error('Select conversation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '載入對話失敗'
            ]);
        }
    }



    public function refreshMessages()
    {
        unset($this->messages);

        if ($this->selectedConversationId) {
            $conversation = Conversation::find($this->selectedConversationId);
            if ($conversation) {
                $conversation->markAsRead(auth()->id());
            }
        }
    }

    public function refreshConversations()
    {
        unset($this->conversations);
    }

    public function sendMessage()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        if (empty(trim($this->messageContent))) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '訊息內容不能為空'
            ]);
            return;
        }

        if (strlen($this->messageContent) > 1000) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '訊息內容不能超過 1000 字'
            ]);
            return;
        }

        try {
            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);

            if (!$conversation) {
                return;
            }

            DB::beginTransaction();

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'text',
                'content' => trim($this->messageContent),
            ]);

            $conversation->updateLastMessage($this->messageContent, auth()->id());

            $otherUser = $conversation->getOtherUser(auth()->id());
            if ($otherUser) {
                $conversation->incrementUnreadCount($otherUser->id);
            }

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            $this->messageContent = '';
            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Send message error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '發送訊息失敗'
            ]);
        }
    }

    public function sendImage()
    {
        if (!$this->selectedConversationId || !$this->uploadedImage) {
            return;
        }

        try {
            $validated = $this->validate([
                'uploadedImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ], [
                'uploadedImage.required' => '請選擇圖片',
                'uploadedImage.image' => '檔案必須是圖片',
                'uploadedImage.mimes' => '只支援 JPEG, PNG, JPG, GIF 格式',
                'uploadedImage.max' => '圖片大小不能超過 5MB',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->validator->errors()->first()
            ]);
            return;
        }

        try {
            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);

            if (!$conversation) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => '對話不存在'
                ]);
                return;
            }

            DB::beginTransaction();

            $path = $this->uploadedImage->store('messages', 'public');

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'image',
                'image_path' => $path,
            ]);

            $conversation->updateLastMessage('[圖片]', auth()->id());

            $otherUser = $conversation->getOtherUser(auth()->id());
            if ($otherUser) {
                $conversation->incrementUnreadCount($otherUser->id);
            }

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            $this->uploadedImage = null;
            $this->dispatch('message-sent');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '圖片已發送'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Send image error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '圖片發送失敗：' . $e->getMessage()
            ]);
        }
    }

    public function toggleBargainPanel()
    {
        $this->showBargainPanel = !$this->showBargainPanel;
    }

    // 🔥 更新：提交議價（含數量）
    public function submitBargain()
    {
        if (!$this->selectedConversationId || !$this->bargainPrice || !$this->bargainQuantity) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '請輸入議價金額和數量'
            ]);
            return;
        }

        if ($this->bargainPrice <= 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '議價金額必須大於 0'
            ]);
            return;
        }

        if ($this->bargainQuantity <= 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '數量必須大於 0'
            ]);
            return;
        }

        try {
            $conversation = Conversation::with(['buyer', 'seller', 'product'])->find($this->selectedConversationId);

            if (!$conversation) {
                return;
            }

            $product = $conversation->product;

            // 🔥 檢查庫存
            if ($product->stock > 0 && $this->bargainQuantity > $product->stock) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "數量超過庫存！目前庫存：{$product->stock}"
                ]);
                return;
            }

            $isBuyer = $conversation->buyer_id === auth()->id();

            DB::beginTransaction();

            // 🔥 計算總價
            $total = $this->bargainPrice * $this->bargainQuantity;

            $bargain = BargainHistory::create([
                'product_id' => $conversation->product_id,
                'conversation_id' => $conversation->id,
                'buyer_id' => $conversation->buyer_id,
                'seller_id' => $conversation->seller_id,
                'original_price' => $product->price,

                // 🔥 買家議價資訊
                'buyer_offer' => $isBuyer ? $this->bargainPrice : null,
                'buyer_quantity' => $isBuyer ? $this->bargainQuantity : null,
                'buyer_total' => $isBuyer ? $total : null,

                // 🔥 賣家議價資訊
                'seller_offer' => !$isBuyer ? $this->bargainPrice : null,
                'seller_quantity' => !$isBuyer ? $this->bargainQuantity : null,
                'seller_total' => !$isBuyer ? $total : null,

                'status' => $isBuyer ? 'pending' : 'countered',
                'round' => $this->getCurrentBargainRound() + 1,
                'offered_at' => now(),
                'expired_at' => now()->addDays(3),
            ]);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'type' => $isBuyer ? 'bargain' : 'bargain_counter',
                'bargain_price' => $this->bargainPrice,
                'bargain_quantity' => $this->bargainQuantity,  // 🔥 儲存數量到訊息
                'related_message_id' => $bargain->id,
            ]);

            $messageText = sprintf(
                '%s：NT$ %s x %d = NT$ %s',
                $isBuyer ? '買家議價' : '賣家反議價',
                number_format($this->bargainPrice),
                $this->bargainQuantity,
                number_format($total)
            );
            $conversation->updateLastMessage($messageText, auth()->id());

            $otherUser = $conversation->getOtherUser(auth()->id());
            if ($otherUser) {
                $conversation->incrementUnreadCount($otherUser->id);
            }

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            $this->bargainPrice = null;
            $this->bargainQuantity = 1;
            $this->showBargainPanel = false;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '議價已送出'
            ]);

            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submit bargain error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '議價失敗'
            ]);
        }
    }

    // 🔥 新增：反議價（賣家）
    public function counterBargain($bargainId)
    {
        if (!$this->counterPrice || !$this->counterQuantity) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '請輸入反議價金額和數量'
            ]);
            return;
        }

        $bargain = BargainHistory::findOrFail($bargainId);

        if ($bargain->conversation_id !== $this->selectedConversationId) {
            return;
        }

        try {
            $conversation = Conversation::with(['buyer', 'seller', 'product'])->find($this->selectedConversationId);
            $product = $conversation->product;

            // 檢查庫存
            if ($product->stock > 0 && $this->counterQuantity > $product->stock) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "數量超過庫存！目前庫存：{$product->stock}"
                ]);
                return;
            }

            DB::beginTransaction();

            $total = $this->counterPrice * $this->counterQuantity;

            $bargain->update([
                'seller_offer' => $this->counterPrice,
                'seller_quantity' => $this->counterQuantity,
                'seller_total' => $total,
                'status' => 'countered',
                'responded_at' => now(),
            ]);

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'bargain_counter',
                'bargain_price' => $this->counterPrice,
                'bargain_quantity' => $this->counterQuantity,
                'related_message_id' => $bargain->id,
            ]);

            $messageText = sprintf(
                '賣家反議價：NT$ %s x %d = NT$ %s',
                number_format($this->counterPrice),
                $this->counterQuantity,
                number_format($total)
            );
            $conversation->updateLastMessage($messageText, auth()->id());

            $otherUser = $conversation->getOtherUser(auth()->id());
            if ($otherUser) {
                $conversation->incrementUnreadCount($otherUser->id);
            }

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            $this->counterPrice = null;
            $this->counterQuantity = null;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '反議價已送出'
            ]);

            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Counter bargain error: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '操作失敗'
            ]);
        }
    }

    public function acceptBargain($bargainId)
    {
        $bargain = BargainHistory::findOrFail($bargainId);

        if ($bargain->conversation_id !== $this->selectedConversationId) {
            return;
        }

        try {
            DB::beginTransaction();

            $finalPrice = $bargain->buyer_offer;
            $finalQuantity = $bargain->buyer_quantity;

            $bargain->update([
                'status' => 'accepted',
                'final_price' => $finalPrice,
                'final_quantity' => $finalQuantity,
                'final_total' => $finalPrice * $finalQuantity,
                'accepted_at' => now(),
            ]);

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'bargain_accept',
                'bargain_price' => $finalPrice,
                'bargain_quantity' => $finalQuantity,
                'related_message_id' => $bargain->id,
            ]);

            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);
            $messageText = sprintf(
                '已接受議價：NT$ %s x %d = NT$ %s',
                number_format($finalPrice),
                $finalQuantity,
                number_format($finalPrice * $finalQuantity)
            );
            $conversation->updateLastMessage($messageText, auth()->id());

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            // 🔥 重新檢查購物車狀態
            $this->checkProductInCart();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已接受議價'
            ]);

            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Accept bargain error: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '操作失敗'
            ]);
        }
    }


    public function rejectBargain($bargainId)
    {
        $bargain = BargainHistory::findOrFail($bargainId);

        if ($bargain->conversation_id !== $this->selectedConversationId) {
            return;
        }

        try {
            DB::beginTransaction();

            $bargain->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'bargain_reject',
                'related_message_id' => $bargain->id,
            ]);

            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);
            $conversation->updateLastMessage('已拒絕議價', auth()->id());

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已拒絕議價'
            ]);

            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject bargain error: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '操作失敗'
            ]);
        }
    }

    public function confirmDeal($bargainId)
    {
        $bargain = BargainHistory::findOrFail($bargainId);

        if ($bargain->conversation_id !== $this->selectedConversationId) {
            return;
        }

        try {
            DB::beginTransaction();

            $finalPrice = $bargain->seller_offer;
            $finalQuantity = $bargain->seller_quantity;

            $bargain->update([
                'status' => 'deal',
                'final_price' => $finalPrice,
                'final_quantity' => $finalQuantity,
                'final_total' => $finalPrice * $finalQuantity,
                'deal_at' => now(),
            ]);

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'bargain_deal',
                'bargain_price' => $finalPrice,
                'bargain_quantity' => $finalQuantity,
                'related_message_id' => $bargain->id,
            ]);

            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);
            $messageText = sprintf(
                '議價成交：NT$ %s x %d = NT$ %s',
                number_format($finalPrice),
                $finalQuantity,
                number_format($finalPrice * $finalQuantity)
            );
            $conversation->updateLastMessage($messageText, auth()->id());

            Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'system',
                'content' => sprintf(
                    '🎉 恭喜！雙方已達成協議，成交價：NT$ %s x %d = NT$ %s。請前往結帳完成交易。',
                    number_format($finalPrice),
                    $finalQuantity,
                    number_format($finalPrice * $finalQuantity)
                ),
            ]);

            DB::commit();

            broadcast(new NewMessageEvent($message))->toOthers();
            broadcast(new ConversationUpdated($conversation));

            $this->showBargainPanel = false;

            // 🔥 重新檢查購物車狀態
            $this->checkProductInCart();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '議價成交！'
            ]);

            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm deal error: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '操作失敗'
            ]);
        }
    }


    protected function getCurrentBargainRound()
    {
        return BargainHistory::where('conversation_id', $this->selectedConversationId)
            ->max('round') ?? 0;
    }

    public function clearChat()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        try {
            Message::where('conversation_id', $this->selectedConversationId)->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '聊天記錄已清除'
            ]);
        } catch (\Exception $e) {
            Log::error('Clear chat error: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '清除失敗'
            ]);
        }
    }
    public function getBargainStatus($bargainId)
    {
        try {
            $bargain = BargainHistory::find($bargainId);
            return $bargain ? $bargain->isAddedToCart() : false;
        } catch (\Exception $e) {
            Log::error('Get bargain status error: ' . $e->getMessage());
            return false;
        }
    }

    public function getDealBargainId($message)
    {
        if ($message->type !== 'bargain_deal') {
            return null;
        }

        return $message->related_message_id;
    }

    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.messaging-component');
    }
}
