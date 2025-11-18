<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\BargainHistory;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class StartConversationButton extends Component
{
    public $productId;
    public $sellerId;
    public $buttonText = '聯繫賣家';
    public $buttonClass = 'px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600';

    public function mount($productId, $sellerId, $buttonText = null, $buttonClass = null)
    {
        $this->productId = $productId;
        $this->sellerId = $sellerId;

        if ($buttonText) {
            $this->buttonText = $buttonText;
        }

        if ($buttonClass) {
            $this->buttonClass = $buttonClass;
        }
    }

    public function startConversation()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 不能跟自己對話
        if ($this->sellerId === auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '無法與自己對話'
            ]);
            return;
        }

        try {
            // 查找所有現有對話
            $existingConversations = Conversation::where('buyer_id', auth()->id())
                ->where('seller_id', $this->sellerId)
                ->where('product_id', $this->productId)
                ->orderBy('id', 'desc')
                ->get();

            if ($existingConversations->isNotEmpty()) {
                foreach ($existingConversations as $conversation) {
                    // 檢查是否有未完成的議價
                    $hasIncompleteBargain = BargainHistory::where('conversation_id', $conversation->id)
                        ->whereNotIn('status', ['completed'])
                        ->exists();

                    if ($hasIncompleteBargain) {
                        // 🔥 使用 redirect() 而不是 return
                        return $this->redirect(route('messages', ['conversationId' => $conversation->id]));
                    }

                    // 檢查是否有任何議價記錄
                    $hasAnyBargain = BargainHistory::where('conversation_id', $conversation->id)->exists();

                    if (!$hasAnyBargain) {
                        return $this->redirect(route('messages', ['conversationId' => $conversation->id]));
                    }
                }

                // 所有對話的議價都已完成，創建新對話
                $conversation = Conversation::create([
                    'buyer_id' => auth()->id(),
                    'seller_id' => $this->sellerId,
                    'product_id' => $this->productId,
                    'status' => 'active',
                ]);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => auth()->id(),
                    'type' => 'system',
                    'content' => '🎉 開始新的議價對話！上次議價已完成，歡迎再次洽談。',
                ]);

                return $this->redirect(route('messages', ['conversationId' => $conversation->id]));
            }

            // 沒有現有對話，創建新對話
            $conversation = Conversation::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $this->sellerId,
                'product_id' => $this->productId,
                'status' => 'active',
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $this->sellerId,
                'type' => 'system',
                'content' => '您好！感謝您的詢問，有任何問題歡迎提出！',
            ]);

            return $this->redirect(route('messages', ['conversationId' => $conversation->id]));

        } catch (\Exception $e) {
            Log::error('Start conversation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '無法建立對話，請稍後再試'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.start-conversation-button');
    }
}
