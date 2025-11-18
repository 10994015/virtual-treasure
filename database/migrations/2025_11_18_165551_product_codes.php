<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // 虛寶序號
            $table->enum('status', ['available', 'reserved', 'sold', 'invalid'])->default('available');

            // 銷售資訊
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sold_at')->nullable(); // 售出時間
            $table->timestamp('reserved_at')->nullable(); // 保留時間（加入購物車）

            // 其他資訊
            $table->text('notes')->nullable(); // 備註
            $table->timestamps();

            // 索引
            $table->index('product_id');
            $table->index('status');
            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_codes');
    }
};
