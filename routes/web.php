<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 乘客叫車頁面（需登入為乘客）
Route::middleware('passenger')->get('/booking', function () {
    return view('booking');
})->name('booking');
