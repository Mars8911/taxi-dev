<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// 乘客建立訂單
Route::post('/orders', [OrderController::class, 'createOrder']);

// 司機取得待接訂單
Route::get('/orders/matching', [OrderController::class, 'getDriverOrders']);

// 額外建議：司機接單 API
Route::patch('/orders/{id}/accept', [OrderController::class, 'acceptOrder']);
