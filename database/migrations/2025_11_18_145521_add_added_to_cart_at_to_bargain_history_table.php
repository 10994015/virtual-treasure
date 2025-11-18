<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bargain_history', function (Blueprint $table) {
            $table->timestamp('added_to_cart_at')->nullable()->after('final_total');
        });
    }

    public function down(): void
    {
        Schema::table('bargain_history', function (Blueprint $table) {
            $table->dropColumn('added_to_cart_at');
        });
    }
};
