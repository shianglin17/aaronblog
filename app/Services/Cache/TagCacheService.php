<?php

namespace App\Services\Cache;

/**
 * 標籤快取服務
 * 
 * 負責處理標籤相關的快取邏輯
 * - 標籤列表快取
 * - 標籤詳情快取
 */
class TagCacheService extends BaseCacheService
{
    /**
     * 快取前綴
     */
    protected string $cachePrefix = 'tags';

    /**
     * 快取時間設定（分鐘）
     * 標籤變動頻率較低，但比分類稍高（新文章可能產生新標籤）
     */
    protected array $cacheTtl = [
        'list' => 4320,   // 標籤列表：3天
        'detail' => 2880, // 標籤詳情：2天
    ];
} 