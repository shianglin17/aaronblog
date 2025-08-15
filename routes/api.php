<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminTagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// API 測試路由
Route::get('/', function() {
    return response()->json(['message' => 'API is working']);
});

// 公開內容 API - 使用 RESTful 資源路由
Route::prefix('articles')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show'])->where('id', '[0-9]+');
});

// 分類相關路由
Route::prefix('categories')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show'])->where('id', '[0-9]+');
});

// 標籤相關路由
Route::prefix('tags')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [TagController::class, 'index']);
    Route::get('/{id}', [TagController::class, 'show'])->where('id', '[0-9]+');
});

// 認證相關路由
Route::prefix('auth')->group(function () {
    // 登入與註冊需要嚴格的 rate limiting
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
    
    // 已認證用戶的路由
    Route::middleware('auth:web')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// 管理後台 API - 需要認證
Route::prefix('admin')->middleware(['auth:web', 'throttle:30,1'])->group(function () {
    // 文章管理（用戶只能管理自己的文章）
    Route::prefix('articles')->group(function () {
        Route::get('/', [AdminArticleController::class, 'index']);
        Route::post('/', [AdminArticleController::class, 'store']);
        Route::put('/{id}', [AdminArticleController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [AdminArticleController::class, 'destroy'])->where('id', '[0-9]+');
    });
    
    // 標籤管理（CUD操作）
    Route::prefix('tags')->group(function () {
        Route::post('/', [AdminTagController::class, 'store']);
        Route::put('/{id}', [AdminTagController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [AdminTagController::class, 'destroy'])->where('id', '[0-9]+');
    });
    
    // 分類管理
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->where('id', '[0-9]+');
    });
});
