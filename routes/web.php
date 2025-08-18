<?php

use Illuminate\Support\Facades\Route;

// CSRF Token 端點 - 為前端 SPA 提供 CSRF Token
// 前端在發送需要 CSRF 保護的請求前，必須先呼叫此端點取得 CSRF Token
// 此端點會在 Cookie 中設定 XSRF-TOKEN 和 session cookie
Route::get('/sanctum/csrf-cookie', function () {
    return response()->noContent();
});

// 將所有前端路由交給Vue處理（排除 api 路徑）
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
