<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * 快取服務抽象基類
 * 
 * 提供通用的快取邏輯實現
 * 使用模板方法模式定義算法骨架
 */
abstract class BaseCacheService implements CacheServiceInterface
{
    /**
     * 快取前綴
     * 子類必須定義此屬性
     */
    protected string $cachePrefix;

    /**
     * 快取時間設定（分鐘）
     * 子類必須定義此屬性
     */
    protected array $cacheTtl;

    /**
     * 快取列表資料
     * 
     * @param callable $callback 資料獲取回調函數
     * @param array $params 快取參數（可選）
     * @return mixed
     */
    public function cacheList(callable $callback, array $params = []): mixed
    {
        $cacheKey = $this->generateListCacheKey($params);
        
        return Cache::tags([$this->getCacheTag('list')])->remember(
            $cacheKey,
            $this->cacheTtl['list'] * 60, // 轉換為秒
            $callback
        );
    }

    /**
     * 快取詳情資料
     * 
     * @param int $id 資源ID
     * @param callable $callback 資料獲取回調函數
     * @return mixed
     */
    public function cacheDetail(int $id, callable $callback): mixed
    {
        $cacheKey = $this->generateDetailCacheKey($id);
        
        return Cache::tags([$this->getCacheTag('detail')])->remember(
            $cacheKey,
            $this->cacheTtl['detail'] * 60, // 轉換為秒
            $callback
        );
    }

    /**
     * 清除列表快取
     * 
     * @return void
     */
    public function clearListCache(): void
    {
        Cache::tags([$this->getCacheTag('list')])->flush();
    }

    /**
     * 清除特定詳情快取
     * 
     * @param int $id 資源ID
     * @return void
     */
    public function clearDetailCache(int $id): void
    {
        $cacheKey = $this->generateDetailCacheKey($id);
        Cache::tags([$this->getCacheTag('detail')])->forget($cacheKey);
    }

    /**
     * 清除特定資源的所有相關快取
     * 
     * @param int $id 資源ID
     * @return void
     */
    public function clearResourceAllCache(int $id): void
    {
        // 清除詳情快取
        $this->clearDetailCache($id);
        
        // 清除列表快取（影響所有列表）
        $this->clearListCache();
    }

    /**
     * 清除所有快取
     * 
     * @return void
     */
    public function clearAllCache(): void
    {
        Cache::tags([$this->getCacheTag('list')])->flush();
        Cache::tags([$this->getCacheTag('detail')])->flush();
    }

    /**
     * 生成快取標籤
     * 
     * @param string $type 快取類型 (list|detail)
     * @return string
     */
    protected function getCacheTag(string $type): string
    {
        return $this->cachePrefix . ':' . $type;
    }

    /**
     * 生成詳情快取鍵
     * 
     * @param int $id 資源ID
     * @return string
     */
    protected function generateDetailCacheKey(int $id): string
    {
        return $this->cachePrefix . ':detail:' . $id;
    }

    /**
     * 生成列表快取鍵
     * 由子類實現具體邏輯
     * 
     * @param array $params 快取參數
     * @return string
     */
    protected function generateListCacheKey(array $params = []): string
    {
        return $this->cachePrefix . ':list:all';
    }
} 