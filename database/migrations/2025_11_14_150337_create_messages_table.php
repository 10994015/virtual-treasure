<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // 關聯對話
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');

            // 發送者
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');

            // 訊息內容
            $table->enum('type', [
                'text',          // 一般文字
                'image',         // 圖片
                'system',        // 系統訊息
                'bargain',       // 議價請求
                'bargain_counter', // 反議價
                'bargain_accept',  // 接受議價
                'bargain_reject',  // 拒絕議價
                'bargain_deal'     // 成交
            ])->default('text');

            $table->text('content')->nullable(); // 訊息內容
            $table->string('image_path')->nullable(); // 圖片路徑

            // 議價相關
            $table->decimal('bargain_price', 10, 2)->nullable(); // 議價金額
            $table->foreignId('related_message_id')->nullable()->constrained('messages'); // 關聯的訊息（用於議價回覆）

            // 訊息狀態
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // 索引
            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index(['conversation_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
