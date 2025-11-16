<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 添加虛寶交易相關欄位
            $table->string('game_server')->nullable()->after('game_type'); // 遊戲伺服器
            $table->string('game_region')->nullable()->after('game_server'); // 遊戲區域
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['game_server', 'game_region']);
        });
    }
};
