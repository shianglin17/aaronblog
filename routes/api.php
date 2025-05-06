<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;


// API 測試路由
Route::get('/', function() {
    return response()->json(['message' => 'API is working']);
});

Route::get('/article/list', [ArticleController::class, 'list']);

// 認證相關路由
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});
