<?php

namespace App\Services\Cache;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 文章快取服務
 * 
 * 負責處理文章相關的快取邏輯
 * - 文章列表快取（支援複雜查詢參數）
 * - 文章詳情快取
 * - 快取失效策略
 */
class ArticleCacheService extends BaseCacheService
{
    /**
     * 快取前綴
     */
    protected string $cachePrefix = 'articles';

    /**
     * 快取時間設定（分鐘）
     */
    protected array $cacheTtl = [
        'list' => 1440,   // 文章列表：24小時
        'detail' => 4320, // 文章詳情：3天
    ];

    /**
     * 快取文章列表
     * 保留此方法因為有特殊的參數處理邏輯
     * 
     * @param callable $callback 資料獲取回調函數
     * @param array $params 查詢參數
     * @return LengthAwarePaginator
     */
    public function cacheArticleList(callable $callback, array $params = []): LengthAwarePaginator
    {
        return $this->cacheList($callback, $params);
    }



    /**
     * 生成文章列表快取鍵
     * 文章列表有複雜的查詢參數處理邏輯
     * 
     * @param array $params 查詢參數
     * @return string
     */
    protected function generateListCacheKey(array $params = []): string
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
        
        return $this->cachePrefix . ':list:' . $signature;
    }
} 