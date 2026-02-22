<?php

use App\Http\Controllers\Passenger\AuthController;
use Illuminate\Support\Facades\Route;

// 乘客註冊／登入（未登入可訪問）
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
