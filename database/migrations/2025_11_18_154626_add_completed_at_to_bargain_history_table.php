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
        Schema::table('bargain_history', function (Blueprint $table) {
            // 🔥 新增 completed_at 欄位（在 added_to_cart_at 之後）
            $table->timestamp('completed_at')->nullable()->after('added_to_cart_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bargain_history', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
