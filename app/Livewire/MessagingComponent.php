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

    public $selectedConversationId = null;
    public $messageContent = '';
    public $searchTerm = '';

    // 議價相關
    public $showBargainPanel = false;
    public $bargainPrice = null;

    // 圖片上傳
    public $uploadedImage = null;

    public function mount()
    {
        //
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

            $conversation->markAsRead(auth()->id());

            $this->showBargainPanel = false;
            $this->bargainPrice = null;
            $this->uploadedImage = null;

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

    // 🔥 從前端呼叫此方法來刷新訊息
    public function refreshMessages()
    {
        // 刷新訊息列表
        unset($this->messages);

        // 標記為已讀
        if ($this->selectedConversationId) {
            $conversation = Conversation::find($this->selectedConversationId);
            if ($conversation) {
                $conversation->markAsRead(auth()->id());
            }
        }
    }

    // 🔥 從前端呼叫此方法來刷新對話列表
    public function refreshConversations()
    {
        // 刷新對話列表
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

            // 廣播訊息事件
            broadcast(new NewMessageEvent($message))->toOthers();

            // 廣播對話更新事件
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

            // 廣播訊息事件
            broadcast(new NewMessageEvent($message))->toOthers();

            // 廣播對話更新事件
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

    public function addBargainToCart($bargainId)
    {
        $bargain = BargainHistory::findOrFail($bargainId);
        if ($bargain->status !== 'deal' && $bargain->status !== 'accepted') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '此議價尚未成交'
            ]);
            return;
        }
        try {
            $cart = [];
            $cartCookie = request()->cookie('shopping_cart');
            if ($cartCookie) {
                $cart = json_decode($cartCookie, true) ?? [];
            }

            $product = $bargain->product;

            $image = null;
            if ($product->images->isNotEmpty()) {
                $primaryImage = $product->images->where('is_primary', true)->first();
                $image = $primaryImage ? $primaryImage->image_path : $product->images->first()->image_path;
            }

            $existingIndex = null;
            foreach ($cart as $index => $item) {
                if ($item['id'] == $product->id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                $cart[$existingIndex]['price'] = $bargain->final_price;
                $cart[$existingIndex]['is_bargain'] = true;
                $cart[$existingIndex]['bargain_id'] = $bargain->id;
            } else {
                $cart[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $bargain->final_price,
                    'quantity' => 1,
                    'image' => '/storage/' . $image,
                    'stock' => $product->stock,
                    'game_type' => $product->game_type,
                    'category' => $product->category,
                    'is_bargain' => true,
                    'bargain_id' => $bargain->id,
                ];
            }

            cookie()->queue('shopping_cart', json_encode($cart), 43200);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已加入購物車（議價價格）'
            ]);

            $this->dispatch('cart-updated', ['count' => count($cart)]);

            return redirect()->route('cart');

        } catch (\Exception $e) {
            Log::error('Add bargain to cart error: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '加入購物車失敗'
            ]);
        }
    }

    public function toggleBargainPanel()
    {
        $this->showBargainPanel = !$this->showBargainPanel;
    }

    public function submitBargain()
    {
        if (!$this->selectedConversationId || !$this->bargainPrice) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '請輸入議價金額'
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

        try {
            $conversation = Conversation::with(['buyer', 'seller', 'product'])->find($this->selectedConversationId);

            if (!$conversation) {
                return;
            }

            $isBuyer = $conversation->buyer_id === auth()->id();

            DB::beginTransaction();

            $bargain = BargainHistory::create([
                'product_id' => $conversation->product_id,
                'conversation_id' => $conversation->id,
                'buyer_id' => $conversation->buyer_id,
                'seller_id' => $conversation->seller_id,
                'original_price' => $conversation->product->price,
                'buyer_offer' => $isBuyer ? $this->bargainPrice : null,
                'seller_offer' => !$isBuyer ? $this->bargainPrice : null,
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
                'related_message_id' => $bargain->id,
            ]);

            $messageText = ($isBuyer ? '買家議價：' : '賣家反議價：') . 'NT$ ' . number_format($this->bargainPrice);
            $conversation->updateLastMessage($messageText, auth()->id());

            $otherUser = $conversation->getOtherUser(auth()->id());
            if ($otherUser) {
                $conversation->incrementUnreadCount($otherUser->id);
            }

            DB::commit();

            // 廣播訊息事件
            broadcast(new NewMessageEvent($message))->toOthers();

            // 廣播對話更新事件
            broadcast(new ConversationUpdated($conversation));

            $this->bargainPrice = null;
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

    public function acceptBargain($bargainId)
    {
        $bargain = BargainHistory::findOrFail($bargainId);

        if ($bargain->conversation_id !== $this->selectedConversationId) {
            return;
        }

        try {
            DB::beginTransaction();

            $finalPrice = $bargain->buyer_offer ?? $bargain->seller_offer;

            $bargain->update([
                'status' => 'accepted',
                'final_price' => $finalPrice,
                'accepted_at' => now(),
            ]);

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'bargain_accept',
                'bargain_price' => $finalPrice,
                'related_message_id' => $bargain->id,
            ]);

            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);
            $conversation->updateLastMessage('已接受議價：NT$ ' . number_format($finalPrice), auth()->id());

            DB::commit();

            // 廣播訊息事件
            broadcast(new NewMessageEvent($message))->toOthers();

            // 廣播對話更新事件
            broadcast(new ConversationUpdated($conversation));

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

            // 廣播訊息事件
            broadcast(new NewMessageEvent($message))->toOthers();

            // 廣播對話更新事件
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

            $finalPrice = $bargain->seller_offer ?? $bargain->buyer_offer;

            $bargain->update([
                'status' => 'deal',
                'final_price' => $finalPrice,
                'deal_at' => now(),
            ]);

            $message = Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'bargain_deal',
                'bargain_price' => $finalPrice,
                'related_message_id' => $bargain->id,
            ]);

            $conversation = Conversation::with(['buyer', 'seller'])->find($this->selectedConversationId);
            $conversation->updateLastMessage('議價成交：NT$ ' . number_format($finalPrice), auth()->id());

            Message::create([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => auth()->id(),
                'type' => 'system',
                'content' => '🎉 恭喜！雙方已達成協議，成交價：NT$ ' . number_format($finalPrice) . '。請前往結帳完成交易。',
            ]);

            DB::commit();

            // 廣播訊息事件
            broadcast(new NewMessageEvent($message))->toOthers();

            // 廣播對話更新事件
            broadcast(new ConversationUpdated($conversation));

            $this->showBargainPanel = false;

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
