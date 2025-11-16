<?php

namespace App\Livewire;

use App\Services\AICustomerService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;

class AIChatComponent extends Component
{
    public $messages = [];
    public $messageInput = '';
    public $isTyping = false;
    public $quickReplies = [];

    protected $aiService;

    public function boot(AICustomerService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function mount()
    {
        // 初始歡迎訊息
        $this->messages[] = [
            'type' => 'bot',
            'content' => '您好！我是 AI 智能客服，很高興為您服務！

我可以幫助您：
- 解答常見問題
- 查詢訂單狀態
- 商品購買指南
- 議價功能說明
- 技術支援

請告訴我您需要什麼幫助？',
            'time' => now()->format('H:i'),
        ];

        // 載入快速回覆
        $this->quickReplies = $this->aiService->getQuickReplies();
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageInput))) {
            return;
        }

        $userMessage = trim($this->messageInput);

        // 添加使用者訊息
        $this->messages[] = [
            'type' => 'user',
            'content' => $userMessage,
            'time' => now()->format('H:i'),
        ];

        // 清空輸入
        $this->messageInput = '';

        // 顯示打字中
        $this->isTyping = true;

        // 模擬延遲 (讓使用者感覺更真實)
        sleep(1);

        // 取得 AI 回覆
        try {
            $response = $this->aiService->handleMessage(
                $userMessage,
                auth()->id()
            );

            $this->messages[] = [
                'type' => 'bot',
                'content' => $response['response'],
                'time' => now()->format('H:i'),
                'source' => $response['source'] ?? 'unknown',
            ];

        } catch (\Exception $e) {
            Log::error('AI Chat error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            $this->messages[] = [
                'type' => 'bot',
                'content' => '抱歉，系統暫時無法回應，請稍後再試或聯絡客服。',
                'time' => now()->format('H:i'),
                'source' => 'error',
            ];
        }

        $this->isTyping = false;

        // 滾動到底部
        $this->dispatch('scroll-to-bottom');
    }

    public function sendQuickReply($message)
    {
        $this->messageInput = $message;
        $this->sendMessage();
    }

    public function clearChat()
    {
        $this->messages = [];
        $this->mount(); // 重新載入歡迎訊息
    }

    #[Title('AI 智能客服')]
    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.ai-chat-component');
    }
}