<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bargain_history', function (Blueprint $table) {
            $table->id();

            // 關聯
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');

            // 議價資訊
            $table->decimal('original_price', 10, 2); // 原始價格
            $table->decimal('buyer_offer', 10, 2)->nullable(); // 買家出價
            $table->decimal('seller_offer', 10, 2)->nullable(); // 賣家出價
            $table->decimal('final_price', 10, 2)->nullable(); // 最終成交價

            // 議價狀態
            $table->enum('status', [
                'pending',      // 待回應
                'countered',    // 已反議價
                'accepted',     // 已接受
                'rejected',     // 已拒絕
                'deal',         // 已成交
                'expired'       // 已過期
            ])->default('pending');

            // 議價輪次
            $table->integer('round')->default(1); // 第幾輪議價

            // 時間記錄
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();

            // 索引
            $table->index('product_id');
            $table->index('conversation_id');
            $table->index(['buyer_id', 'seller_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bargain_history');
    }
};
