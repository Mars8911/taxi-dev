<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 叫車訂單模型
 *
 * 關聯：passenger (User), driver (User)
 * 狀態流程：matching -> ongoing -> completed
 * 車資計算：total_price = distance (km) × 50 元/公里
 */
class Order extends Model
{
    use HasFactory;

    /**
     * 每公里車資（台幣）
     */
    public const PRICE_PER_KM = 50;

    /**
     * 可大量賦值的欄位
     *
     * @var list<string>
     */
    protected $fillable = [
        'passenger_id',
        'driver_id',
        'start_location',
        'end_location',
        'distance',
        'total_price',
        'status',
    ];

    /**
     * 型態轉換
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'distance' => 'float',
            'total_price' => 'integer',
        ];
    }

    /**
     * 訂單所屬乘客
     */
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    /**
     * 訂單所屬司機（配對前為 null）
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
