<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 此遷移用於更新現有的 bargain_history 表，新增數量支援功能
     */
    public function up(): void
    {
        Schema::table('bargain_history', function (Blueprint $table) {
            // ==================== 新增數量相關欄位 ====================

            // 買家數量資訊
            if (!Schema::hasColumn('bargain_history', 'buyer_quantity')) {
                $table->integer('buyer_quantity')->nullable()
                    ->after('buyer_offer')
                    ->comment('買家想購買的數量');
            }

            if (!Schema::hasColumn('bargain_history', 'buyer_total')) {
                $table->decimal('buyer_total', 10, 2)->nullable()
                    ->after('buyer_quantity')
                    ->comment('買家提出的總價（單價 x 數量）');
            }

            // 賣家數量資訊
            if (!Schema::hasColumn('bargain_history', 'seller_quantity')) {
                $table->integer('seller_quantity')->nullable()
                    ->after('seller_offer')
                    ->comment('賣家同意的數量');
            }

            if (!Schema::hasColumn('bargain_history', 'seller_total')) {
                $table->decimal('seller_total', 10, 2)->nullable()
                    ->after('seller_quantity')
                    ->comment('賣家提出的總價（單價 x 數量）');
            }

            // 最終成交數量資訊
            if (!Schema::hasColumn('bargain_history', 'final_quantity')) {
                $table->integer('final_quantity')->nullable()
                    ->after('final_price')
                    ->comment('最終成交數量');
            }

            if (!Schema::hasColumn('bargain_history', 'final_total')) {
                $table->decimal('final_total', 10, 2)->nullable()
                    ->after('final_quantity')
                    ->comment('最終成交總價（單價 x 數量）');
            }

            // ==================== 新增時間欄位 ====================

            if (!Schema::hasColumn('bargain_history', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()
                    ->after('responded_at')
                    ->comment('賣家接受時間');
            }

            if (!Schema::hasColumn('bargain_history', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()
                    ->after('accepted_at')
                    ->comment('拒絕時間');
            }

            if (!Schema::hasColumn('bargain_history', 'deal_at')) {
                $table->timestamp('deal_at')->nullable()
                    ->after('rejected_at')
                    ->comment('買家確認成交時間');
            }

            // ==================== 更新欄位註解 ====================

            // 更新價格欄位的註解，明確說明是「單價」而非「總價」
            $table->decimal('original_price', 10, 2)->comment('商品原始單價')->change();
            $table->decimal('buyer_offer', 10, 2)->nullable()->comment('買家提出的單價')->change();
            $table->decimal('seller_offer', 10, 2)->nullable()->comment('賣家提出的單價')->change();
            $table->decimal('final_price', 10, 2)->nullable()->comment('最終成交單價')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bargain_history', function (Blueprint $table) {
            // 移除新增的欄位
            $columns = [
                'buyer_quantity',
                'buyer_total',
                'seller_quantity',
                'seller_total',
                'final_quantity',
                'final_total',
                'accepted_at',
                'rejected_at',
                'deal_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bargain_history', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
