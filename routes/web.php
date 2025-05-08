<?php

use Illuminate\Support\Facades\Route;

// 將所有前端路由交給Vue處理
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
