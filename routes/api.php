<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;


// API 測試路由
Route::get('/', function() {
    return response()->json(['message' => 'API is working']);
});

// 公開文章路由
Route::get('/article/list', [ArticleController::class, 'list']);
Route::get('/article/{id}', [ArticleController::class, 'show'])->where('id', '[0-9]+');

// 分類和標籤路由
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);

// 認證相關路由
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// 需要認證的文章管理路由
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        // 文章管理
        Route::prefix('article')->group(function () {
            Route::post('/', [ArticleController::class, 'store']);
            Route::put('/{id}', [ArticleController::class, 'update'])->where('id', '[0-9]+');
            Route::delete('/{id}', [ArticleController::class, 'destroy'])->where('id', '[0-9]+');
            Route::patch('/{id}/publish', [ArticleController::class, 'publish'])->where('id', '[0-9]+');
            Route::patch('/{id}/draft', [ArticleController::class, 'draft'])->where('id', '[0-9]+');
        });
    });
});
