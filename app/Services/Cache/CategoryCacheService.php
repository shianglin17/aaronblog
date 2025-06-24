<?php

namespace App\Services\Cache;

/**
 * 分類快取服務
 * 
 * 負責處理分類相關的快取邏輯
 * - 分類列表快取
 * - 分類詳情快取
 */
class CategoryCacheService extends BaseCacheService
{
    /**
     * 快取前綴
     */
    protected string $cachePrefix = 'categories';

    /**
     * 快取時間設定（分鐘）
     * 分類變動頻率很低，可以設定更長時間
     */
    protected array $cacheTtl = [
        'list' => 10080,  // 分類列表：7天
        'detail' => 7200, // 分類詳情：5天
    ];
} 