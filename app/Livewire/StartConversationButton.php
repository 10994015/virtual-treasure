<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

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

        // 檢查是否已有對話
        $conversation = Conversation::where('buyer_id', auth()->id())
            ->where('seller_id', $this->sellerId)
            ->where('product_id', $this->productId)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $this->sellerId,
                'product_id' => $this->productId,
                'status' => 'active',
            ]);

            // 建立歡迎訊息
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $this->sellerId,
                'type' => 'system',
                'content' => '您好！感謝您的詢問，有任何問題歡迎提出！',
            ]);
        }

        return redirect()->route('messages')->with('selectConversation', $conversation->id);
    }

    public function render()
    {
        return view('livewire.start-conversation-button');
    }
}
