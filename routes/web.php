<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleViewController;
use App\Http\Controllers\HomeController;

// CSRF Token 端點 - 為前端 SPA 提供 CSRF Token
// 前端在發送需要 CSRF 保護的請求前，必須先呼叫此端點取得 CSRF Token
// 此端點會在 Cookie 中設定 XSRF-TOKEN 和 session cookie
Route::get('/sanctum/csrf-cookie', function () {
    return response()->noContent();
});

// ========================================
// SSR 路由 - 優先於 SPA 路由
// ========================================

// 首頁 SSR - 優先於 SPA
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// 文章詳情頁面 SSR - 用於 SEO 優化
Route::get('/article/{slug}', [ArticleViewController::class, 'show'])
    ->name('article.show')
    ->where('slug', '[a-zA-Z0-9\-_]+');

// ========================
// SPA 路由 - 處理所有其他頁面
// ========================================

// 將所有其他前端路由交給Vue處理（排除 api 路徑）
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
