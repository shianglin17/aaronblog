<?php

use App\Exceptions\BaseException;
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
        // Session Cookie 認證中間件配置
        $middleware->web(append: [
            // 網頁請求的中間件
        ]);
    
        $middleware->api(append: [
            // SPA Session Cookie 認證中間件
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            EnsureFrontendRequestsAreStateful::class,
            'throttle:30,1',
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

        // 處理自定義業務異常
        $exceptions->render(function (BaseException $e, Request $request) {
            if ($request->expectsJson()) {
                return $e->toJSONResponse();
            }
        });
    })->create();
