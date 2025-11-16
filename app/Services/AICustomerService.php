<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AICustomerService
{
    protected $localFAQ;
    
    public function __construct(LocalFAQService $localFAQ)
    {
        $this->localFAQ = $localFAQ;
    }
    
    /**
     * 處理使用者訊息
     */
    public function handleMessage(string $userMessage, ?int $userId = null): array
    {
        // 記錄使用者訊息
        Log::info('AI Customer Service - User Message', [
            'user_id' => $userId,
            'message' => $userMessage,
        ]);

        // 1️⃣ 先嘗試本地知識庫
        $localResponse = $this->localFAQ->getResponse($userMessage);
        
        if ($localResponse) {
            Log::info('Local FAQ matched', [
                'category' => $localResponse['category'],
                'user_id' => $userId,
            ]);
            
            return [
                'response' => $localResponse['response'],
                'source' => 'local',
                'category' => $localResponse['category'],
                'confidence' => 0.95,
            ];
        }
        
        // 2️⃣ 本地無法回答,使用 OpenAI (如果啟用)
        if (config('services.openai.enabled', false)) {
            return $this->getOpenAIResponse($userMessage, $userId);
        }
        
        // 3️⃣ 預設回覆
        return $this->getFallbackResponse();
    }
    
    /**
     * OpenAI 回覆 (可選功能)
     */
    protected function getOpenAIResponse(string $userMessage, ?int $userId = null): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('OpenAI response received', [
                    'user_id' => $userId,
                    'tokens' => $data['usage']['total_tokens'] ?? 0,
                ]);
                
                return [
                    'response' => $data['choices'][0]['message']['content'] ?? '無法取得回應',
                    'source' => 'openai',
                    'confidence' => 0.8,
                    'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                ];
            }
            
            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'user_id' => $userId,
            ]);
            
        } catch (\Exception $e) {
            Log::error('OpenAI Exception', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);
        }
        
        return $this->getFallbackResponse();
    }
    
    /**
     * 預設回覆
     */
    protected function getFallbackResponse(): array
    {
        $responses = [
            '抱歉，我目前無法回答這個問題。讓我為您轉接人工客服！

📧 Email: support@example.com
💬 線上客服: 週一至週五 09:00-18:00
📞 客服專線: (02) 1234-5678

或者您可以試試重新描述您的問題，我會盡力協助您！',
        ];

        return [
            'response' => $responses[0],
            'source' => 'fallback',
            'confidence' => 0,
        ];
    }
    
    /**
     * OpenAI 系統提示詞
     */
    protected function getSystemPrompt(): string
    {
        return '你是一個虛擬寶物交易平台的專業客服助理。

平台資訊：
- 名稱：虛擬寶物交易平台
- 服務：遊戲虛寶、道具、皮膚、材料交易
- 特色：議價功能、即時通訊、安全交易保障

回答規則：
1. 使用繁體中文
2. 保持專業且友善的語氣
3. 回答要簡潔明瞭，使用分點列表
4. 適當使用表情符號增加親和力 (💬📦✅等)
5. 如果不確定，請建議聯絡人工客服
6. 涉及金額/退款/個資等重要問題，請謹慎回答
7. 不要編造不存在的功能或政策

請根據使用者問題提供有幫助的回答。';
    }

    /**
     * 取得快速回覆
     */
    public function getQuickReplies(): array
    {
        return $this->localFAQ->getQuickReplies();
    }
}

