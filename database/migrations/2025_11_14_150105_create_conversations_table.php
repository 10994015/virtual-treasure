<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // 對話雙方
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // 買家
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // 賣家

            // 關聯商品
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // 對話狀態
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');

            // 最後訊息資訊（用於列表顯示）
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->foreignId('last_message_by')->nullable()->constrained('users');

            // 未讀數量
            $table->integer('buyer_unread_count')->default(0);
            $table->integer('seller_unread_count')->default(0);

            $table->timestamps();

            // 索引
            $table->index(['buyer_id', 'seller_id', 'product_id']);
            $table->index('product_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
