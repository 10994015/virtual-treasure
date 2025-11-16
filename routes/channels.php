<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// 用戶私有頻道 - 用於接收對話列表更新
Broadcast::channel('user.{userId}', function ($user, $userId) {
    Log::info('🔐 [Channel Auth] user.' . $userId, [
        'authenticated_user_id' => $user->id,
        'requested_user_id' => $userId,
        'match' => (int) $user->id === (int) $userId,
    ]);
    
    return (int) $user->id === (int) $userId;
});

// 對話私有頻道 - 用於接收即時訊息
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    Log::info('🔐 [Channel Auth] conversation.' . $conversationId, [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
    ]);
    
    // 檢查用戶是否有權限訪問此對話
    $conversation = \App\Models\Conversation::find($conversationId);
    
    if (!$conversation) {
        Log::warning('❌ [Channel Auth] Conversation not found', [
            'conversation_id' => $conversationId,
        ]);
        return false;
    }
    
    $isBuyer = (int) $user->id === (int) $conversation->buyer_id;
    $isSeller = (int) $user->id === (int) $conversation->seller_id;
    $authorized = $isBuyer || $isSeller;
    
    Log::info('🔐 [Channel Auth] Result', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'buyer_id' => $conversation->buyer_id,
        'seller_id' => $conversation->seller_id,
        'is_buyer' => $isBuyer,
        'is_seller' => $isSeller,
        'authorized' => $authorized,
    ]);
    
    return $authorized;
});