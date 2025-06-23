<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;

/**
 * 分類快取服務
 * 
 * 負責處理分類相關的快取邏輯
 * - 分類列表快取
 * - 分類詳情快取
 */
class CategoryCacheService
{
    /**
     * 快取前綴
     */
    private const CACHE_PREFIX = 'categories';

    /**
     * 快取時間設定（分鐘）
     * 分類變動頻率很低，可以設定更長時間
     */
    private const CACHE_TTL = [
        'list' => 10080,  // 分類列表：7天
        'detail' => 7200, // 分類詳情：5天
    ];

    /**
     * 生成分類列表快取鍵
     * 
     * @return string
     */
    public function generateListCacheKey(): string
    {
        return self::CACHE_PREFIX . ':list:all';
    }

    /**
     * 生成分類詳情快取鍵
     * 
     * @param int $categoryId 分類ID
     * @return string
     */
    public function generateDetailCacheKey(int $categoryId): string
    {
        return self::CACHE_PREFIX . ':detail:' . $categoryId;
    }

    /**
     * 快取分類列表
     * 
     * @param callable $callback 資料獲取回調函數
     * @return Collection
     */
    public function cacheCategoryList(callable $callback): Collection
    {
        $cacheKey = $this->generateListCacheKey();
        
        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL['list'] * 60,
            $callback
        );
    }

    /**
     * 快取分類詳情
     * 
     * @param int $categoryId 分類ID
     * @param callable $callback 資料獲取回調函數
     * @return Category
     */
    public function cacheCategoryDetail(int $categoryId, callable $callback): Category
    {
        $cacheKey = $this->generateDetailCacheKey($categoryId);
        
        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL['detail'] * 60,
            $callback
        );
    }

    /**
     * 清除分類列表快取
     */
    public function clearListCache(): void
    {
        $cacheKey = $this->generateListCacheKey();
        Cache::forget($cacheKey);
    }

    /**
     * 清除特定分類的詳情快取
     * 
     * @param int $categoryId 分類ID
     */
    public function clearCategoryDetailCache(int $categoryId): void
    {
        $cacheKey = $this->generateDetailCacheKey($categoryId);
        Cache::forget($cacheKey);
    }

    /**
     * 清除特定分類的所有相關快取
     * 
     * @param int $categoryId 分類ID
     */
    public function clearCategoryAllCache(int $categoryId): void
    {
        // 清除詳情快取
        $this->clearCategoryDetailCache($categoryId);
        
        // 清除列表快取
        $this->clearListCache();
    }
} 