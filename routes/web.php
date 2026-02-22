<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 乘客叫車頁面（手機網頁）
Route::get('/booking', function () {
    return view('booking');
})->name('booking');
