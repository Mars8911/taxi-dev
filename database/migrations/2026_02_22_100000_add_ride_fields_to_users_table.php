<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 擴充 Users 表 - 叫車系統所需欄位
 *
 * 新增欄位：
 * - phone: 手機號碼，唯一索引（用於登入／聯絡）
 * - role: 角色 (passenger=乘客, driver=司機)
 * - is_blacklisted: 是否在黑名單中（預設 false，用於配對過濾）
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->enum('role', ['passenger', 'driver'])->default('passenger')->after('phone');
            $table->boolean('is_blacklisted')->default(false)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'is_blacklisted']);
        });
    }
};
