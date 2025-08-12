<?php

namespace App\Services\Cache;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 後台文章快取服務
 * 
 * 負責處理後台管理專用的文章快取邏輯
 * - 用戶隔離的文章列表快取
 * - 用戶隔離的文章詳情快取
 * - 管理專用的快取失效策略
 */
class AdminArticleCacheService extends BaseCacheService
{
    /**
     * 快取前綴（後台專用）
     */
    protected string $cachePrefix = 'articles:admin';

    /**
     * 快取時間設定（分鐘）
     */
    protected array $cacheTtl = [
        'list' => 60,     // 管理列表：1小時（更頻繁更新）
        'detail' => 240,  // 管理詳情：4小時
    ];

    /**
     * 快取用戶文章列表
     * 
     * @param callable $callback 資料獲取回調函數
     * @param array $params 查詢參數（包含 user_id）
     * @return LengthAwarePaginator
     */
    public function cacheUserArticleList(callable $callback, array $params = []): LengthAwarePaginator
    {
        $cacheKey = $this->generateListCacheKey($params);
        $userId = $params['user_id'] ?? Auth::id();
        
        return \Illuminate\Support\Facades\Cache::tags([$this->cachePrefix . "_user_{$userId}_list"])->remember(
            $cacheKey,
            $this->cacheTtl['list'] * 60,
            $callback
        );
    }

    /**
     * 快取用戶文章詳情
     * 
     * @param int $id 文章ID
     * @param callable $callback 資料獲取回調函數
     * @param int $userId 用戶ID
     * @return mixed
     */
    public function cacheUserArticleDetail(int $id, callable $callback, int $userId)
    {
        $cacheKey = $this->generateUserDetailCacheKey($id, $userId);
        
        return \Illuminate\Support\Facades\Cache::tags([$this->cachePrefix . "_user_{$userId}_detail"])->remember(
            $cacheKey,
            $this->cacheTtl['detail'] * 60,
            $callback
        );
    }

    /**
     * 生成用戶文章列表快取鍵
     * 包含用戶隔離邏輯
     * 
     * @param array $params 查詢參數
     * @return string
     */
    protected function generateListCacheKey(array $params = []): string
    {
        $userId = $params['user_id'] ?? Auth::id();
        
        // 標準化參數，確保快取鍵一致
        $normalizedParams = [
            'user_id' => $userId,
            'search' => $params['search'] ?? '',
            'status' => $params['status'] ?? 'all',
            'category' => $params['category'] ?? '',
            'tags' => is_array($params['tags'] ?? null) ? implode(',', $params['tags']) : '',
            'sort_by' => $params['sort_by'] ?? 'created_at',
            'sort_direction' => $params['sort_direction'] ?? 'desc',
            'per_page' => $params['per_page'] ?? 15,
            'page' => $params['page'] ?? 1,
        ];

        // 生成簽名
        $signature = md5(serialize($normalizedParams));
        
        return $this->cachePrefix . ":user_{$userId}:list:" . $signature;
    }

    /**
     * 生成用戶文章詳情快取鍵
     * 
     * @param int $id 文章ID
     * @param int $userId 用戶ID
     * @return string
     */
    protected function generateUserDetailCacheKey(int $id, int $userId): string
    {
        return $this->cachePrefix . ":user_{$userId}:detail:{$id}";
    }

    /**
     * 清除用戶相關的所有快取
     * 
     * @param int|null $userId 用戶ID，為空則使用當前認證用戶
     * @return void
     */
    public function clearUserCache(?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return;
        }

        // 清除用戶的列表快取（使用 Laravel Cache 清除模式）
        \Illuminate\Support\Facades\Cache::tags([$this->cachePrefix . "_user_{$userId}_list"])->flush();
        
        // 清除用戶的詳情快取
        \Illuminate\Support\Facades\Cache::tags([$this->cachePrefix . "_user_{$userId}_detail"])->flush();
    }

    /**
     * 清除特定用戶的特定文章快取
     * 
     * @param int $articleId 文章ID
     * @param int|null $userId 用戶ID
     * @return void
     */
    public function clearUserArticleCache(int $articleId, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return;
        }

        // 清除該用戶的列表快取（因為文章可能影響列表）
        \Illuminate\Support\Facades\Cache::tags([$this->cachePrefix . "_user_{$userId}_list"])->flush();
        
        // 清除特定文章的詳情快取
        $detailKey = $this->generateUserDetailCacheKey($articleId, $userId);
        \Illuminate\Support\Facades\Cache::forget($detailKey);
    }

    /**
     * 實作介面要求的 clearListCache 方法
     */
    public function clearListCache(): void
    {
        // 對於 Admin 版本，清除所有用戶的列表快取會太過激進
        // 這裡可以清除當前用戶的快取，或提供警告
        $userId = Auth::id();
        if ($userId) {
            \Illuminate\Support\Facades\Cache::tags([$this->cachePrefix . "_user_{$userId}_list"])->flush();
        }
    }

    /**
     * 實作介面要求的 clearDetailCache 方法
     */
    public function clearDetailCache(int $id): void
    {
        // 對於 Admin 版本，只清除當前用戶的該文章快取
        $userId = Auth::id();
        if ($userId) {
            $detailKey = $this->generateUserDetailCacheKey($id, $userId);
            \Illuminate\Support\Facades\Cache::forget($detailKey);
        }
    }

    /**
     * 實作介面要求的 clearResourceAllCache 方法
     */
    public function clearResourceAllCache(int $id): void
    {
        $userId = Auth::id();
        if ($userId) {
            $this->clearUserArticleCache($id, $userId);
        }
    }

    /**
     * 實作介面要求的 clearAllCache 方法
     */
    public function clearAllCache(): void
    {
        // 只清除當前用戶的所有快取
        $this->clearUserCache();
    }
}