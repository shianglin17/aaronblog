<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\AdminArticleController;
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
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:web');
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
    
    // 標籤管理
    Route::prefix('tags')->group(function () {
        Route::post('/', [TagController::class, 'store']);
        Route::put('/{id}', [TagController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [TagController::class, 'destroy'])->where('id', '[0-9]+');
    });
    
    // 分類管理
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->where('id', '[0-9]+');
    });
});
