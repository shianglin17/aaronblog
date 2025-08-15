<?php

namespace App\Services;

use App\Models\Article;

/**
 * 權限檢查服務
 * 
 * 統一處理應用程式中的權限驗證邏輯
 */
class AuthorizationService
{
    /**
     * 檢查用戶是否可以修改指定文章
     * 
     * @param Article $article 文章實例
     * @param int $userId 用戶 ID
     * @return bool
     */
    public function canModifyArticle(Article $article, int $userId): bool
    {
        return $article->user_id === $userId;
    }

    /**
     * 檢查用戶是否可以刪除指定文章
     * 
     * @param Article $article 文章實例
     * @param int $userId 用戶 ID
     * @return bool
     */
    public function canDeleteArticle(Article $article, int $userId): bool
    {
        return $article->user_id === $userId;
    }

    /**
     * 檢查用戶是否為超級管理員
     * 
     * @param int $userId 用戶 ID
     * @return bool
     */
    public function isSuperAdmin(int $userId): bool
    {
        $superAdmins = config('auth.super_admins', [1]);
        
        // 支援逗號分隔的字串格式
        if (is_string($superAdmins)) {
            $superAdmins = array_map('intval', explode(',', $superAdmins));
        }
        
        return in_array($userId, $superAdmins);
    }

    /**
     * 檢查用戶是否可以管理全域資源（標籤、分類等）
     * 
     * @param int $userId 用戶 ID
     * @return bool
     */
    public function canManageGlobalResources(int $userId): bool
    {
        return $this->isSuperAdmin($userId);
    }
}