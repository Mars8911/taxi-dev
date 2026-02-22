<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 建立 Orders 表 - 叫車訂單
 *
 * 欄位說明：
 * - passenger_id: 乘客 ID (FK -> users)
 * - driver_id: 司機 ID (FK -> users, nullable，配對完成前為 null)
 * - start_location: 出發地（地址或經緯度字串）
 * - end_location: 目的地
 * - distance: 行程距離（公里，float）
 * - total_price: 總價（台幣，每公里 50 元 × distance）
 * - status: 訂單狀態 (matching=配對中, ongoing=進行中, completed=已完成, cancelled=已取消)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('start_location');
            $table->string('end_location');
            $table->float('distance')->default(0);
            $table->unsignedInteger('total_price')->default(0);
            $table->enum('status', ['matching', 'ongoing', 'completed', 'cancelled'])->default('matching');
            $table->timestamps();

            // 索引：常用於查詢司機／乘客的訂單
            $table->index(['passenger_id', 'status']);
            $table->index(['driver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
