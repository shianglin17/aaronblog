<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\ApiResponse;
use App\Services\AuthorizationService;

/**
 * AdminOnly Middleware
 * 
 * 限制只有主帳號（超級管理員）可以執行特定操作
 * 主要用於保護標籤和分類的管理功能
 */
class AdminOnly
{
    /**
     * @param AuthorizationService $authorizationService
     */
    public function __construct(
        protected readonly AuthorizationService $authorizationService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 檢查用戶是否已登入
        if (!Auth::check()) {
            return ApiResponse::forbidden('請先登入');
        }

        // 檢查是否為超級管理員
        if (!$this->authorizationService->isSuperAdmin(Auth::id())) {
            return ApiResponse::forbidden('權限不足，只有主帳號可以執行此操作');
        }

        return $next($request);
    }
}