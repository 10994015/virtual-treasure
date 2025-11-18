<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🔥 修改 status 欄位，新增 'completed' 狀態
        DB::statement("ALTER TABLE `bargain_history` MODIFY `status` ENUM('pending', 'accepted', 'rejected', 'countered', 'deal', 'completed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔥 回滾：移除 'completed' 狀態
        // 注意：這會將所有 'completed' 的記錄改為 'deal'
        DB::statement("UPDATE `bargain_history` SET `status` = 'deal' WHERE `status` = 'completed'");
        DB::statement("ALTER TABLE `bargain_history` MODIFY `status` ENUM('pending', 'accepted', 'rejected', 'countered', 'deal') NOT NULL DEFAULT 'pending'");
    }
};
