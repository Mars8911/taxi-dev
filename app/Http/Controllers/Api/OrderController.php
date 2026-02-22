<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 訂單 API 控制器
 *
 * 提供：
 * - createOrder: 乘客建立訂單（計算車資，狀態為 matching）
 * - getDriverOrders: 司機取得所有 status=matching 的訂單
 *
 * 註：需整合 auth（如 Sanctum）後，以 auth()->id() 取得 passenger_id / driver_id。
 * 目前可透過 Request 傳入 passenger_id / driver_id 進行開發測試。
 */
class OrderController extends Controller
{
    /**
     * 1. 建立訂單 - 乘客輸入起點終點，後端計算車資
     *
     * 流程：
     * - 驗證起點、終點與經緯度
     * - 檢查黑名單（is_blacklisted），封鎖用戶不得叫車
     * - 使用 Haversine 公式計算距離（公里）
     * - 車資試算 = 距離 × 50 元/公里
     * - 建立訂單，status 設為 matching
     *
     * Request body:
     * - start_location (string): 起點地址／顯示名稱
     * - end_location (string): 終點地址／顯示名稱
     * - passenger_id (int): 乘客 ID（TODO: 整合 auth 後改為 auth()->id()）
     * - distance (float, optional): 前端傳入的距離（公里），有此欄位則直接使用，不計算 Haversine
     * - start_lat, start_lng, end_lat, end_lng (float): 無 distance 時必填，用於 Haversine 計算
     *
     * @return JsonResponse
     */
    public function createOrder(Request $request): JsonResponse
    {
        // === 參數驗證 ===
        $validated = $request->validate([
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'passenger_id' => 'nullable|integer|exists:users,id',
            'distance' => 'nullable|numeric|min:0',
            'start_lat' => 'required_without:distance|nullable|numeric|between:-90,90',
            'start_lng' => 'required_without:distance|nullable|numeric|between:-180,180',
            'end_lat' => 'required_without:distance|nullable|numeric|between:-90,90',
            'end_lng' => 'required_without:distance|nullable|numeric|between:-180,180',
        ]);

        // 至少需有 distance 或經緯度其一
        if (empty($validated['distance']) && empty($validated['start_lat'])) {
            return response()->json([
                'success' => false,
                'message' => '請提供 distance 或起終點經緯度。',
            ], 422);
        }

        // 若未傳 passenger_id，預設為 1（開發用，整合 auth 後改為 auth()->id()）
        $passengerId = $validated['passenger_id'] ?? 1;

        // === 2. 檢查黑名單（架構師提醒：安全第一）===
        $passenger = User::findOrFail($passengerId);
        if ($passenger->is_blacklisted) {
            return response()->json([
                'success' => false,
                'message' => '您的帳號已被封鎖，無法叫車。',
            ], 403);
        }

        // 驗證乘客為 passenger 角色（若有 role 欄位）
        if (isset($passenger->role) && $passenger->role !== 'passenger') {
            return response()->json([
                'success' => false,
                'message' => '僅乘客可建立訂單。',
            ], 403);
        }

        // === 計算距離（前端傳入 distance 則直接使用，否則用 Haversine）===
        if (! empty($validated['distance'])) {
            $distanceKm = (float) $validated['distance'];
        } else {
            $distanceKm = $this->calculateHaversineDistance(
                (float) $validated['start_lat'],
                (float) $validated['start_lng'],
                (float) $validated['end_lat'],
                (float) $validated['end_lng']
            );
        }

        // === 計算車資（每公里 50 元）===
        $totalPrice = (int) ceil($distanceKm * Order::PRICE_PER_KM);

        // === 建立訂單 ===
        $order = Order::create([
            'passenger_id' => $passengerId,
            'driver_id' => null,
            'start_location' => $validated['start_location'],
            'end_location' => $validated['end_location'],
            'distance' => round($distanceKm, 2),
            'total_price' => $totalPrice,
            'status' => 'matching',
        ]);

        return response()->json([
            'success' => true,
            'message' => '訂單建立成功，等待司機接單。',
            'data' => [
                'order_id' => $order->id,
                'distance_km' => $order->distance,
                'total_price' => $order->total_price,
                'status' => $order->status,
            ],
        ], 201);
    }

    /**
     * 2. 司機取得所有 status=matching 的訂單
     *
     * 用於司機端列表，顯示待接單訂單。
     * 後續可加上 Haversine 過濾：只顯示司機位置附近的訂單。
     *
     * Query params:
     * - driver_id (int, optional): 司機 ID（TODO: 整合 auth 後改為 auth()->id()）
     *
     * @return JsonResponse
     */
    public function getDriverOrders(Request $request): JsonResponse
    {
        // 若傳入 driver_id 可驗證司機角色（可選）
        $driverId = $request->query('driver_id');
        if ($driverId !== null) {
            $driver = User::find($driverId);
            if ($driver && isset($driver->role) && $driver->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => '僅司機可查詢待接單訂單。',
                ], 403);
            }
        }

        // 查詢所有 status=matching 的訂單
        $orders = Order::query()
            ->where('status', 'matching')
            ->with('passenger:id,name,phone')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders->map(fn (Order $o) => [
                'id' => $o->id,
                'passenger' => $o->passenger ? [
                    'id' => $o->passenger->id,
                    'name' => $o->passenger->name,
                    'phone' => $o->passenger->phone ?? null,
                ] : null,
                'start_location' => $o->start_location,
                'end_location' => $o->end_location,
                'distance_km' => $o->distance,
                'total_price' => $o->total_price,
                'created_at' => $o->created_at?->toIso8601String(),
            ]),
        ]);
    }

    /**
     * 使用 Haversine 公式計算兩點間距離（公里）
     *
     * @param float $lat1 起點緯度
     * @param float $lon1 起點經度
     * @param float $lat2 終點緯度
     * @param float $lon2 終點經度
     * @return float 距離（公里）
     */
    private function calculateHaversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadiusKm = 6371.0;

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dlat = $lat2Rad - $lat1Rad;
        $dlon = $lon2Rad - $lon1Rad;

        $a = sin($dlat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
