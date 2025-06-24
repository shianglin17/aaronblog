<?php

namespace App\Services\Cache;

/**
 * 快取服務介面
 * 
 * 定義所有快取服務必須實現的方法契約
 */
interface CacheServiceInterface
{
    /**
     * 快取列表資料
     * 
     * @param callable $callback 資料獲取回調函數
     * @param array $params 快取參數（可選）
     * @return mixed
     */
    public function cacheList(callable $callback, array $params = []): mixed;

    /**
     * 快取詳情資料
     * 
     * @param int $id 資源ID
     * @param callable $callback 資料獲取回調函數
     * @return mixed
     */
    public function cacheDetail(int $id, callable $callback): mixed;

    /**
     * 清除列表快取
     * 
     * @return void
     */
    public function clearListCache(): void;

    /**
     * 清除特定詳情快取
     * 
     * @param int $id 資源ID
     * @return void
     */
    public function clearDetailCache(int $id): void;

    /**
     * 清除特定資源的所有相關快取
     * 
     * @param int $id 資源ID
     * @return void
     */
    public function clearResourceAllCache(int $id): void;

    /**
     * 清除所有快取
     * 
     * @return void
     */
    public function clearAllCache(): void;
} 