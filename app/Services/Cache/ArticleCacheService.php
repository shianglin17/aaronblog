<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Article;

/**
 * 文章快取服務
 * 
 * 負責處理文章相關的快取邏輯
 * - 文章列表快取
 * - 文章詳情快取
 * - 快取失效策略
 */
class ArticleCacheService
{
    /**
     * 快取前綴
     */
    private const CACHE_PREFIX = 'articles';

    /**
     * 快取時間設定（分鐘）
     */
    private const CACHE_TTL = [
        'list' => 1440,   // 文章列表：24小時
        'detail' => 4320, // 文章詳情：3天
    ];

    /**
     * 生成文章列表快取鍵
     * 
     * @param array $params 查詢參數
     * @return string
     */
    public function generateListCacheKey(array $params): string
    {
        // 標準化參數，確保快取鍵一致
        $normalizedParams = [
            'search' => $params['search'] ?? '',
            'status' => $params['status'] ?? 'all',
            'category' => $params['category'] ?? '',
            'tags' => is_array($params['tags'] ?? null) ? implode(',', $params['tags']) : '',
            'sort_by' => $params['sort_by'] ?? 'created_at',
            'sort_direction' => $params['sort_direction'] ?? 'desc',
            'per_page' => $params['per_page'] ?? 10,
            'page' => $params['page'] ?? 1,
        ];

        // 生成簽名
        $signature = md5(serialize($normalizedParams));
        
        return self::CACHE_PREFIX . ':list:' . $signature;
    }

    /**
     * 生成文章詳情快取鍵
     * 
     * @param int $articleId 文章ID
     * @return string
     */
    public function generateDetailCacheKey(int $articleId): string
    {
        return self::CACHE_PREFIX . ':detail:' . $articleId;
    }

    /**
     * 快取文章列表
     * 
     * @param array $params 查詢參數
     * @param callable $callback 資料獲取回調函數
     * @return LengthAwarePaginator
     */
    public function cacheArticleList(array $params, callable $callback): LengthAwarePaginator
    {
        $cacheKey = $this->generateListCacheKey($params);
        
        // 使用標籤快取，以便批量清除
        return Cache::tags([self::CACHE_PREFIX . ':list'])->remember(
            $cacheKey,
            self::CACHE_TTL['list'] * 60, // 轉換為秒
            $callback
        );
    }

    /**
     * 快取文章詳情
     * 
     * @param int $articleId 文章ID
     * @param callable $callback 資料獲取回調函數
     * @return Article|null
     */
    public function cacheArticleDetail(int $articleId, callable $callback): ?Article
    {
        $cacheKey = $this->generateDetailCacheKey($articleId);
        
        // 使用標籤快取，以便批量清除
        return Cache::tags([self::CACHE_PREFIX . ':detail'])->remember(
            $cacheKey,
            self::CACHE_TTL['detail'] * 60, // 轉換為秒
            $callback
        );
    }

    /**
     * 清除所有文章列表快取
     * 
     * 當有文章新增、修改、刪除時呼叫
     */
    public function clearAllListCache(): void
    {
        // 使用標籤快取策略清除所有列表快取
        Cache::tags([self::CACHE_PREFIX . ':list'])->flush();
    }

    /**
     * 清除特定文章的詳情快取
     * 
     * @param int $articleId 文章ID
     */
    public function clearArticleDetailCache(int $articleId): void
    {
        $cacheKey = $this->generateDetailCacheKey($articleId);
        Cache::forget($cacheKey);
    }

    /**
     * 清除特定文章的所有相關快取
     * 
     * @param int $articleId 文章ID
     */
    public function clearArticleAllCache(int $articleId): void
    {
        // 清除詳情快取
        $this->clearArticleDetailCache($articleId);
        
        // 清除列表快取（影響所有列表）
        $this->clearAllListCache();
    }

    /**
     * 清除所有文章相關快取（列表和詳情）
     * 
     * 用於大量資料異動時的快取重置
     */
    public function clearAllCache(): void
    {
        Cache::tags([self::CACHE_PREFIX . ':list'])->flush();
        Cache::tags([self::CACHE_PREFIX . ':detail'])->flush();
    }
} 