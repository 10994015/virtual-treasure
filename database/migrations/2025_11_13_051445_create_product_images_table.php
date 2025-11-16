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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('image_path'); // 圖片路徑
            $table->string('thumbnail_path')->nullable(); // 縮圖路徑
            $table->integer('order')->default(0); // 排序
            $table->boolean('is_primary')->default(false); // 是否為主圖
            $table->string('alt_text')->nullable(); // 圖片替代文字
            $table->timestamps();

            $table->index('product_id');
            $table->index(['product_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
