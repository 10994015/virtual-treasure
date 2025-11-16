<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // 基本資訊
            $table->string('name'); // 商品名稱
            $table->string('slug')->unique(); // SEO友好的URL slug
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 賣家ID

            // 分類資訊
            $table->string('category'); // 商品類別 (例如: 武器、裝備、道具等)
            $table->string('game_type'); // 遊戲類型 (例如: LOL, 原神, etc)
            $table->enum('rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary', 'mythic'])->default('common'); // 稀有度

            // 商品詳情
            $table->text('description'); // 商品描述
            $table->text('specifications')->nullable(); // 商品規格/屬性 (JSON格式)

            // 價格相關
            $table->decimal('price', 10, 2); // 售價
            $table->decimal('original_price', 10, 2)->nullable(); // 原價(用於顯示折扣)
            $table->decimal('min_price', 10, 2)->nullable(); // 最低參考價格
            $table->decimal('max_price', 10, 2)->nullable(); // 最高參考價格
            $table->boolean('is_negotiable')->default(false); // 是否允許議價

            // 庫存相關
            $table->integer('stock')->default(0); // 庫存數量

            // 商品狀態
            $table->enum('status', ['draft', 'active', 'inactive', 'sold_out', 'suspended'])->default('draft'); // 商品狀態
            $table->boolean('is_published')->default(false); // 是否上架
            $table->boolean('is_featured')->default(false); // 是否為精選商品

            // 交易相關
            $table->enum('delivery_method', ['instant', 'manual', 'both'])->default('manual'); // 交付方式
            $table->text('delivery_instructions')->nullable(); // 交付說明
            $table->integer('delivery_time')->nullable(); // 預計交付時間(分鐘)

            // 審核相關
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending'); // 審核狀態
            $table->text('rejection_reason')->nullable(); // 拒絕原因
            $table->timestamp('verified_at')->nullable(); // 審核時間

            // 時間戳記
            $table->timestamp('published_at')->nullable(); // 上架時間
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (軟刪除)

            // 索引優化
            $table->index('user_id');
            $table->index('category');
            $table->index('game_type');
            $table->index('rarity');
            $table->index('status');
            $table->index('is_published');
            $table->index('created_at');
            $table->index(['is_published', 'status']); // 複合索引
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
