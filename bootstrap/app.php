<?php

use App\Exceptions\ApiException;
use App\Http\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $middleware->web(append: []);
        $middleware->api(append: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            EnsureFrontendRequestsAreStateful::class,
            'throttle:30,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 統一 API 錯誤處理
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!$request->expectsJson()) {
                return null; // 讓 Laravel 處理 web 請求
            }

            return match (true) {
                $e instanceof ValidationException => ApiResponse::validationError($e->errors()),
                $e instanceof ModelNotFoundException => ApiResponse::notFound(),
                $e instanceof AuthorizationException => ApiResponse::forbidden($e->getMessage()),
                $e instanceof ApiException => $e->toResponse(),
                default => null // 讓 Laravel 處理其他異常
            };
        });
    })->create();