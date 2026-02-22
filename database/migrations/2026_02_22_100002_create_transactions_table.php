<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 建立 Transactions 表 - 搭車付款明細
 *
 * 欄位說明：
 * - order_id: 訂單 ID (FK -> orders)
 * - user_id: 付款人 ID (FK -> users)
 * - amount: 付款金額（台幣）
 * - payment_method: 付款方式 (linepay, credit_card, 等)
 * - transaction_id: 三方支付單號（待付款時為 null）
 * - status: 交易狀態 (pending=待處理, success=成功, failed=失敗)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->string('payment_method', 50);
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();

            // 索引：查詢訂單付款紀錄、依狀態篩選
            $table->index(['order_id']);
            $table->index(['user_id', 'status']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
