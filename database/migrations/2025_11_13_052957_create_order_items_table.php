<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // 訂單ID
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // 商品ID
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // 賣家ID

            // 商品快照（防止商品被修改或刪除後影響訂單記錄）
            $table->string('product_name'); // 商品名稱
            $table->string('product_category')->nullable(); // 商品類別
            $table->string('game_type')->nullable(); // 遊戲類型
            $table->string('rarity')->nullable(); // 稀有度
            $table->text('product_description')->nullable(); // 商品描述
            $table->text('product_specifications')->nullable(); // 商品規格（JSON）
            $table->string('product_image')->nullable(); // 商品圖片

            // 虛寶交易資訊
            $table->string('game_server')->nullable(); // 遊戲伺服器
            $table->string('game_region')->nullable(); // 遊戲區域

            // 價格資訊
            $table->decimal('original_price', 10, 2)->nullable()->change(); // 改為可為 null
            $table->decimal('price', 10, 2); // 實際單價（議價後）
            $table->integer('quantity')->default(1); // 數量
            $table->decimal('subtotal', 10, 2); // 小計

            // 交付資訊
            $table->enum('delivery_method', ['instant', 'manual', 'both'])->nullable(); // 交付方式
            $table->text('delivery_instructions')->nullable(); // 交付說明
            $table->enum('delivery_status', ['pending', 'processing', 'delivered', 'failed'])->default('pending'); // 交付狀態
            $table->text('delivery_code')->nullable(); // 交付代碼/序號
            $table->text('delivery_info')->nullable(); // 交付資訊（JSON）
            $table->timestamp('delivered_at')->nullable(); // 交付時間

            // 評價
            $table->integer('rating')->nullable(); // 1-5 星
            $table->text('review')->nullable(); // 評論內容
            $table->timestamp('reviewed_at')->nullable(); // 評價時間

            $table->timestamps();

            // 索引
            $table->index('order_id');
            $table->index('product_id');
            $table->index('seller_id');
            $table->index('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
