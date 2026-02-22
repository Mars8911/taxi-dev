<?php

use App\Http\Controllers\Driver\AuthController;
use App\Http\Controllers\Driver\DashboardController;
use Illuminate\Support\Facades\Route;

// 司機註冊／登入（未登入可訪問）
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// 司機儀表板（需司機身份）
Route::middleware('driver')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
