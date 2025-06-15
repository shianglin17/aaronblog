<?php

use App\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 添加 Sanctum 中間件
        $middleware->web(append: [
            // 網頁請求的中間件
        ]);
    
        $middleware->api(append: [
            // API 請求的中間件
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
        ]);
    
        // 添加命名中間件
        $middleware->alias([
            'auth.sanctum' => EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 使用自定義異常處理程序
        $exceptions->dontReport([
            // 這裡可以添加不需要報告的異常類型
        ]);

        // 處理驗證異常
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => '驗證錯誤',
                    'meta' => [
                        'errors' => $e->errors()
                    ]
                ], 422);
            }
        });
    })->create();
