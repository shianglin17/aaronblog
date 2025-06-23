<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Tag;

/**
 * 標籤快取服務
 * 
 * 負責處理標籤相關的快取邏輯
 * - 標籤列表快取
 * - 標籤詳情快取
 */
class TagCacheService
{
    /**
     * 快取前綴
     */
    private const CACHE_PREFIX = 'tags';

    /**
     * 快取時間設定（分鐘）
     * 標籤變動頻率較低，但比分類稍高（新文章可能產生新標籤）
     */
    private const CACHE_TTL = [
        'list' => 4320,   // 標籤列表：3天
        'detail' => 2880, // 標籤詳情：2天
    ];

    /**
     * 生成標籤列表快取鍵
     * 
     * @return string
     */
    public function generateListCacheKey(): string
    {
        return self::CACHE_PREFIX . ':list:all';
    }

    /**
     * 生成標籤詳情快取鍵
     * 
     * @param int $tagId 標籤ID
     * @return string
     */
    public function generateDetailCacheKey(int $tagId): string
    {
        return self::CACHE_PREFIX . ':detail:' . $tagId;
    }

    /**
     * 快取標籤列表
     * 
     * @param callable $callback 資料獲取回調函數
     * @return Collection
     */
    public function cacheTagList(callable $callback): Collection
    {
        $cacheKey = $this->generateListCacheKey();
        
        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL['list'] * 60,
            $callback
        );
    }

    /**
     * 快取標籤詳情
     * 
     * @param int $tagId 標籤ID
     * @param callable $callback 資料獲取回調函數
     * @return Tag
     */
    public function cacheTagDetail(int $tagId, callable $callback): Tag
    {
        $cacheKey = $this->generateDetailCacheKey($tagId);
        
        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL['detail'] * 60,
            $callback
        );
    }

    /**
     * 清除標籤列表快取
     */
    public function clearListCache(): void
    {
        $cacheKey = $this->generateListCacheKey();
        Cache::forget($cacheKey);
    }

    /**
     * 清除特定標籤的詳情快取
     * 
     * @param int $tagId 標籤ID
     */
    public function clearTagDetailCache(int $tagId): void
    {
        $cacheKey = $this->generateDetailCacheKey($tagId);
        Cache::forget($cacheKey);
    }

    /**
     * 清除特定標籤的所有相關快取
     * 
     * @param int $tagId 標籤ID
     */
    public function clearTagAllCache(int $tagId): void
    {
        // 清除詳情快取
        $this->clearTagDetailCache($tagId);
        
        // 清除列表快取
        $this->clearListCache();
    }
} 