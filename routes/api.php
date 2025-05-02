<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

// API 測試路由
Route::get('/', function() {
    return response()->json(['message' => 'API is working']);
});

Route::get('/article/list', [ArticleController::class, 'list']);
