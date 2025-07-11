# Redis 快取策略

## 概述

Aaron Blog 採用 Redis 作為主要的快取解決方案，通過分層快取架構提升應用程式效能。Redis 配置針對 GCP 免費層資源進行了優化，實現了高效的記憶體管理和快取策略。

## Redis 架構設計

### 快取層次結構

```
┌─────────────────────────────────────┐
│            Redis Cache              │
├─────────────────────────────────────┤
│  Database 0: Default (Session)     │
│  ├── Session Storage               │
│  ├── Queue Jobs                   │
│  └── General Purpose              │
├─────────────────────────────────────┤
│  Database 1: Cache                 │
│  ├── Article Cache                │
│  ├── Category Cache               │
│  ├── Tag Cache                    │
│  └── API Response Cache           │
└─────────────────────────────────────┘
```

### 資料庫分離策略

**Database 0 (Default)**
- **用途**: Session、Queue、一般快取
- **TTL**: 動態設定，基於業務需求
- **清理策略**: LRU (Least Recently Used)

**Database 1 (Cache)**
- **用途**: 應用程式專用快取
- **TTL**: 結構化設定，分類管理
- **清理策略**: 標籤式清理

## 快取服務架構

### 服務分層

```
┌─────────────────────────────────────┐
│        Cache Service Layer          │
├─────────────────────────────────────┤
│  ArticleCacheService               │
│  ├── cacheList()                  │
│  ├── cacheDetail()                │
│  ├── clearListCache()             │
│  └── clearDetailCache()           │
├─────────────────────────────────────┤
│  BaseCacheService                  │
│  ├── 模板方法模式                   │
│  ├── 快取鍵生成                     │
│  ├── TTL 管理                      │
│  └── 標籤管理                      │
├─────────────────────────────────────┤
│  CacheServiceInterface            │
│  ├── 快取介面定義                   │
│  └── 標準化操作                     │
└─────────────────────────────────────┘
```

### 快取服務實作

**BaseCacheService 抽象類**
```php
abstract class BaseCacheService implements CacheServiceInterface
{
    protected string $cachePrefix;
    protected array $cacheTtl;
    
    public function cacheList(callable $callback, array $params = []): mixed
    {
        $cacheKey = $this->generateListCacheKey($params);
        
        return Cache::tags([$this->getCacheTag('list')])->remember(
            $cacheKey,
            $this->cacheTtl['list'] * 60,
            $callback
        );
    }
    
    public function cacheDetail(int $id, callable $callback): mixed
    {
        $cacheKey = $this->generateDetailCacheKey($id);
        
        return Cache::tags([$this->getCacheTag('detail')])->remember(
            $cacheKey,
            $this->cacheTtl['detail'] * 60,
            $callback
        );
    }
}
```

**ArticleCacheService 具體實作**
```php
class ArticleCacheService extends BaseCacheService
{
    protected string $cachePrefix = 'article';
    protected array $cacheTtl = [
        'list' => 30,    // 30 分鐘
        'detail' => 60,  // 60 分鐘
    ];
    
    protected function generateListCacheKey(array $params = []): string
    {
        return $this->cachePrefix . ':list:' . md5(serialize($params));
    }
    
    protected function generateDetailCacheKey(int $id): string
    {
        return $this->cachePrefix . ':detail:' . $id;
    }
}
```

## 快取策略

### TTL (Time To Live) 設定

**分層 TTL 策略**
```php
'article' => [
    'list' => 30,      // 文章列表：30 分鐘
    'detail' => 60,    // 文章詳情：60 分鐘
],
'category' => [
    'list' => 120,     // 分類列表：2 小時
    'detail' => 240,   // 分類詳情：4 小時
],
'tag' => [
    'list' => 60,      // 標籤列表：1 小時
    'detail' => 120,   // 標籤詳情：2 小時
],
```

**動態 TTL 調整**
```php
public function getDynamicTtl(string $type, array $context = []): int
{
    $baseTtl = $this->cacheTtl[$type];
    
    // 根據訪問頻率調整
    if ($context['high_traffic'] ?? false) {
        return $baseTtl * 2; // 高流量時延長快取時間
    }
    
    // 根據內容更新頻率調整
    if ($context['frequently_updated'] ?? false) {
        return $baseTtl / 2; // 頻繁更新時縮短快取時間
    }
    
    return $baseTtl;
}
```

### 標籤式快取管理

**標籤結構**
```php
// 文章相關標籤
'article:list'     // 文章列表快取
'article:detail'   // 文章詳情快取
'article:search'   // 搜尋結果快取

// 分類相關標籤
'category:list'    // 分類列表快取
'category:detail'  // 分類詳情快取

// 標籤相關標籤
'tag:list'         // 標籤列表快取
'tag:detail'       // 標籤詳情快取
```

**標籤式清理**
```php
// 清理所有文章列表快取
Cache::tags(['article:list'])->flush();

// 清理特定文章詳情快取
Cache::tags(['article:detail'])->forget('article:detail:123');

// 清理相關聯的快取
public function clearRelatedCache(int $articleId): void
{
    // 清理文章詳情快取
    $this->clearDetailCache($articleId);
    
    // 清理文章列表快取
    $this->clearListCache();
    
    // 清理相關分類快取
    $article = Article::find($articleId);
    if ($article && $article->category) {
        Cache::tags(['category:detail'])->forget(
            'category:detail:' . $article->category_id
        );
    }
}
```

## 效能優化

### 記憶體優化

**記憶體限制設定**
```bash
# docker-compose.gcp.yml
redis:
  image: redis:7-alpine
  command: redis-server --maxmemory 100mb --maxmemory-policy allkeys-lru
  mem_limit: 128m
```

**記憶體使用監控**
```php
public function getMemoryUsage(): array
{
    $redis = Redis::connection();
    $info = $redis->info('memory');
    
    return [
        'used_memory' => $info['used_memory'],
        'used_memory_human' => $info['used_memory_human'],
        'used_memory_peak' => $info['used_memory_peak'],
        'used_memory_peak_human' => $info['used_memory_peak_human'],
        'maxmemory' => $info['maxmemory'],
        'maxmemory_human' => $info['maxmemory_human'],
    ];
}
```

### 快取預熱

**預熱策略**
```php
class CacheWarmupService
{
    public function warmupArticleCache(): void
    {
        // 預熱熱門文章
        $popularArticles = Article::orderBy('views', 'desc')
            ->take(10)
            ->get();
            
        foreach ($popularArticles as $article) {
            $this->articleCacheService->cacheDetail(
                $article->id,
                fn() => $article->load(['category', 'tags'])
            );
        }
        
        // 預熱文章列表
        $this->articleCacheService->cacheList(
            fn() => Article::with(['category', 'tags'])
                ->latest()
                ->paginate(10)
        );
    }
    
    public function warmupCategoryCache(): void
    {
        // 預熱分類列表
        $categories = Category::withCount('articles')->get();
        
        Cache::tags(['category:list'])->put(
            'category:list:all',
            $categories,
            120 * 60 // 2 小時
        );
    }
}
```

### 快取穿透防護

**空值快取**
```php
public function getArticleWithNullCache(int $id): ?Article
{
    $cacheKey = "article:detail:$id";
    
    return Cache::tags(['article:detail'])->remember(
        $cacheKey,
        $this->cacheTtl['detail'] * 60,
        function() use ($id) {
            $article = Article::find($id);
            
            // 即使是 null 也要快取，防止快取穿透
            return $article ?: 'NULL_PLACEHOLDER';
        }
    );
}
```

**布隆過濾器（概念）**
```php
class BloomFilterService
{
    public function mightExist(int $articleId): bool
    {
        // 簡化的布隆過濾器實作
        $key = "bloom:article:$articleId";
        return Redis::exists($key);
    }
    
    public function add(int $articleId): void
    {
        $key = "bloom:article:$articleId";
        Redis::setex($key, 3600, '1'); // 1 小時過期
    }
}
```

## 監控與維護

### 效能監控

**快取命中率監控**
```php
class CacheMetricsService
{
    public function getCacheHitRate(): array
    {
        $redis = Redis::connection();
        $info = $redis->info('stats');
        
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        
        return [
            'hits' => $hits,
            'misses' => $misses,
            'total' => $total,
            'hit_rate' => $total > 0 ? ($hits / $total) * 100 : 0,
        ];
    }
    
    public function getTopKeys(): array
    {
        $redis = Redis::connection();
        
        // 獲取所有鍵
        $keys = $redis->keys('*');
        
        // 按 TTL 排序
        $keyInfo = [];
        foreach ($keys as $key) {
            $ttl = $redis->ttl($key);
            $keyInfo[] = [
                'key' => $key,
                'ttl' => $ttl,
                'type' => $redis->type($key),
            ];
        }
        
        return $keyInfo;
    }
}
```

### 健康檢查

**Redis 連接檢查**
```php
public function healthCheck(): array
{
    try {
        $redis = Redis::connection();
        $response = $redis->ping();
        
        return [
            'status' => 'healthy',
            'response_time' => $this->measureResponseTime(),
            'memory_usage' => $this->getMemoryUsage(),
            'cache_hit_rate' => $this->getCacheHitRate(),
        ];
    } catch (Exception $e) {
        return [
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
        ];
    }
}

private function measureResponseTime(): float
{
    $start = microtime(true);
    Redis::connection()->ping();
    return (microtime(true) - $start) * 1000; // 毫秒
}
```

### 清理策略

**定期清理**
```php
class CacheCleanupService
{
    public function cleanupExpiredKeys(): void
    {
        $redis = Redis::connection();
        
        // Redis 會自動清理過期鍵，但可以手動觸發
        $redis->eval("
            local keys = redis.call('keys', '*')
            local expired = 0
            for i=1,#keys do
                if redis.call('ttl', keys[i]) == -2 then
                    expired = expired + 1
                end
            end
            return expired
        ", 0);
    }
    
    public function cleanupByPattern(string $pattern): int
    {
        $redis = Redis::connection();
        $keys = $redis->keys($pattern);
        
        if (empty($keys)) {
            return 0;
        }
        
        return $redis->del($keys);
    }
}
```

## 環境配置

### 開發環境配置

**docker-compose.yml**
```yaml
redis:
  image: redis:7-alpine
  ports:
    - "6379:6379"
  command: redis-server --appendonly yes
  volumes:
    - redis_data:/data
```

**環境變數**
```env
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DB=0
REDIS_CACHE_DB=1
CACHE_STORE=redis
```

### 生產環境配置

**docker-compose.gcp.yml**
```yaml
redis:
  image: redis:7-alpine
  command: redis-server --maxmemory 100mb --maxmemory-policy allkeys-lru --appendonly yes
  mem_limit: 128m
  volumes:
    - redis_data:/data
  restart: unless-stopped
```

**優化參數**
```bash
# 記憶體優化
--maxmemory 100mb
--maxmemory-policy allkeys-lru

# 持久化設定
--appendonly yes
--appendfsync everysec

# 網路優化
--tcp-keepalive 60
--timeout 300
```

## 最佳實踐

### 快取設計原則

1. **分層快取**
   - 不同類型的資料使用不同的快取策略
   - 根據訪問頻率設定不同的 TTL

2. **標籤管理**
   - 使用標籤進行快取分組
   - 便於批量清理相關快取

3. **防護機制**
   - 實作快取穿透防護
   - 設定合理的快取大小限制

### 效能優化技巧

1. **預熱策略**
   - 應用啟動時預熱熱門資料
   - 定期更新預熱資料

2. **記憶體管理**
   - 監控記憶體使用情況
   - 設定合理的淘汰策略

3. **監控告警**
   - 監控快取命中率
   - 設定記憶體使用告警

### 維護建議

1. **定期檢查**
   - 監控 Redis 效能指標
   - 分析快取使用模式

2. **容量規劃**
   - 根據業務增長調整配置
   - 預留足夠的記憶體空間

3. **備份策略**
   - 定期備份重要快取資料
   - 測試快取恢復流程

---

*此 Redis 快取策略確保了 Aaron Blog 在有限資源下實現最佳的快取效能和穩定性。* 