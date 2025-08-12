<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * 認證用戶相關功能 Trait
 * 
 * 提供控制器中常用的認證用戶操作
 */
trait AuthenticatedUser
{
    /**
     * 獲取當前認證用戶 ID
     */
    protected function getCurrentUserId(): int
    {
        return Auth::id();
    }

    /**
     * 將當前用戶 ID 加入到數據陣列中
     */
    protected function withCurrentUser(array $data): array
    {
        return array_merge($data, ['user_id' => $this->getCurrentUserId()]);
    }

    /**
     * 將當前用戶 ID 加入到參數陣列中
     */
    protected function withCurrentUserParams(array $params): array
    {
        return array_merge($params, ['user_id' => $this->getCurrentUserId()]);
    }
}