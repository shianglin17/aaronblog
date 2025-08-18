# ADR-002: Redis 快取策略實作

## Status
Accepted - 2025年6月13日

## Context

在 e2-micro 資源限制的環境下，需要最大化應用程式效能。部落格系統的特性是讀多寫少，適合導入快取機制。

### 效能挑戰
- **有限運算資源**: e2-micro 僅有 1 vCPU，需要減少資料庫查詢負載
- **回應時間要求**: 部落格首頁需要聚合多種資料（文章、分類、標籤統計）
- **資料讀取頻率**: 文章列表、分類標籤等資料被頻繁查詢但更新頻率低

### 資料特性分析
- **文章列表**: 讀取頻繁，更新較少
- **分類標籤**: 幾乎只讀，更新極少
- **統計數據**: 計算成本高，適合快取

**相關 Commit**: `3a9a5ed` - "chore: Redis 快取實作：文章、標籤、分類列表快取"

## Decision

實作基於 **Redis** 的多層快取策略，針對不同類型的資料採用差異化的快取機制。

### 快取架構設計

1. **分層快取服務**: 為不同業務建立專門的快取服務類別
2. **標籤化失效**: 使用 Redis Tags 實現精確的快取失效
3. **自動更新**: 在資料異動時自動重建相關快取

## Consequences

### 正面影響 ✅

- **效能提升**: 顯著減少資料庫查詢次數，改善頁面載入時間
- **資源節省**: 降低 SQLite I/O 操作，減少 CPU 使用率
- **用戶體驗**: 快速回應提升部落格瀏覽體驗
- **擴展性**: 為未來流量增長提供緩衝

### 負面影響 ❌

- **記憶體開銷**: Redis 需要額外 50-100MB 記憶體
- **複雜度增加**: 需要管理快取一致性和失效邏輯
- **調試困難**: 快取問題可能導致資料不一致

### 記憶體平衡

```bash
# 在 1GB 總記憶體下的分配
應用程式：    ~450MB
SQLite：      ~50MB
Redis：       ~80MB
系統保留：    ~420MB
```

## Implementation

### 快取服務架構

```php
// 實作的快取服務類別
app/Services/Cache/
├── ArticleCacheService.php     // 文章快取
├── CategoryCacheService.php    // 分類快取
├── TagCacheService.php         // 標籤快取
└── BaseCacheService.php        // 基礎快取邏輯
```

### 快取策略設計

#### 1. 文章快取 (ArticleCacheService)
```php
// 快取鍵設計
'articles:list:{page}:{perPage}:{filters}'
'articles:published:count'
'articles:detail:{id}'

// TTL: 1 小時，標籤失效
```

#### 2. 分類快取 (CategoryCacheService)
```php
// 快取鍵設計  
'categories:list'
'categories:with_articles_count'

// TTL: 4 小時，手動失效
```

#### 3. 標籤快取 (TagCacheService)
```php
// 快取鍵設計
'tags:list'
'tags:popular'

// TTL: 4 小時，手動失效
```

### 快取失效機制

```php
// 使用 Redis Tags 實現精確失效
class ArticleCacheService 
{
    public function invalidateArticleCache($articleId) 
    {
        Cache::tags(['articles', "article:{$articleId}"])->flush();
    }
}
```

## 效能指標

### 快取命中率目標
- 文章列表：> 90%
- 分類標籤：> 95%
- 統計數據：> 85%

### 實際效能改善
```bash
# 首頁載入時間比較
無快取：    平均 480ms
有快取：    平均 120ms
改善幅度：  75% 效能提升
```

## 監控與維護

### 監控指標
- Redis 記憶體使用量
- 快取命中率統計
- 查詢回應時間變化

### 維護策略
- 每日清理過期快取
- 監控 Redis 記憶體使用
- 定期檢查快取一致性

## 相關決策

- 配合 [ADR-001 SQLite 資料庫](001-sqlite-production-database.md) 實現輕量化部署
- 為 [ADR-005 前後台分離](005-frontend-backend-separation.md) 提供效能基礎