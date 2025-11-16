<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // 訂單編號

            // 買家資訊 - 改為 user_id 統一
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // 買家ID (允許訪客購買)

            // 訂單金額
            $table->decimal('subtotal', 10, 2); // 小計（商品總額）
            $table->decimal('total', 10, 2); // 訂單總金額

            // 優惠券/折扣碼
            $table->string('coupon_code')->nullable(); // 優惠券代碼
            $table->decimal('coupon_discount', 10, 2)->default(0); // 優惠券折扣金額

            // 訂單狀態
            $table->enum('status', [
                'pending',          // 待付款
                'paid',            // 已付款
                'processing',      // 處理中
                'delivering',      // 交付中
                'completed',       // 已完成
                'cancelled',       // 已取消
                'refunding',       // 退款中
                'refunded',        // 已退款
                'dispute'          // 爭議中
            ])->default('pending');

            // 付款資訊
            $table->enum('payment_method', [
                'credit_card',
                'atm',
                'convenience_store',
                'wallet'
            ])->nullable(); // 付款方式
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending'); // 付款狀態
            $table->string('payment_transaction_id')->nullable(); // 付款交易ID
            $table->timestamp('paid_at')->nullable(); // 付款時間

            // 買家資訊
            $table->string('buyer_name'); // 買家姓名
            $table->string('buyer_email'); // 買家信箱
            $table->string('buyer_phone')->nullable(); // 買家電話
            $table->string('buyer_game_id')->nullable(); // 買家遊戲ID

            // 備註
            $table->text('buyer_note')->nullable(); // 買家備註
            $table->text('admin_note')->nullable(); // 管理員備註

            // 取消/退款資訊
            $table->text('cancellation_reason')->nullable(); // 取消原因
            $table->foreignId('cancelled_by')->nullable()->constrained('users'); // 誰取消的
            $table->timestamp('cancelled_at')->nullable(); // 取消時間

            $table->decimal('refund_amount', 10, 2)->nullable(); // 退款金額
            $table->text('refund_reason')->nullable(); // 退款原因
            $table->timestamp('refunded_at')->nullable(); // 退款時間

            // 時間戳記
            $table->timestamp('completed_at')->nullable(); // 完成時間
            $table->timestamp('expires_at')->nullable(); // 訂單過期時間
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at

            // 索引優化
            $table->index('order_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
