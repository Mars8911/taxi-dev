<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// 乘客建立訂單
Route::post('/orders', [OrderController::class, 'createOrder']);

// 司機取得待接訂單
Route::get('/orders/driver', [OrderController::class, 'getDriverOrders']);

// 司機接單
Route::post('/orders/{id}/accept', [OrderController::class, 'acceptOrder']);
