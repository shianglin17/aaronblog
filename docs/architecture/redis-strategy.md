# Redis 快取架構

## 概述

Aaron Blog 的 Redis 配置針對 GCP e2-micro 環境優化，實現 100MB 記憶體限制下的高效快取策略。設計重點在於應用程式快取、Session 存儲管理，以及與 Laravel 的深度整合。

> **注意**: 根據實際配置檢查，Session 是存在 SQLite 資料庫中，但 Redis 主要用於應用程式快取

## Redis 架構設計

### 雙資料庫策略

```
┌─────────────────────────────────────┐
│     Redis 7-alpine (100MB limit)   │
├─────────────────────────────────────┤
│  Database 0: Laravel 預設           │
│  ├── Queue Jobs (database driver)  │
│  ├── Cache Tags 管理               │
│  └── 一般用途快取                   │
├─────────────────────────────────────┤
│  Database 1: 應用程式快取            │
│  ├── aaronblog_cache_articles:*    │
│  ├── aaronblog_cache_categories:*  │
│  ├── aaronblog_cache_tags:*        │
│  └── 標籤式快取管理                 │
└─────────────────────────────────────┘
```

### 實際快取使用情況

**主要快取項目** (基於實際資料)
- `aaronblog_cache_articles:list:*` - 文章列表快取
- `aaronblog_cache_articles:detail:*` - 文章詳情快取  
- `aaronblog_cache_categories:list:all` - 分類列表快取
- `aaronblog_cache_tags:list:all` - 標籤列表快取

**快取回退機制**
- Redis 不可用時自動回退到 SQLite cache 表
- 開發環境中可能同時存在 Redis 和 Database cache

## 快取服務架構

### Laravel Cache Tags 整合

**BaseCacheService 架構**
- **模板方法模式**: 統一的快取操作介面
- **標籤管理**: `Cache::tags()` 實現分組快取清理
- **TTL 策略**: 文章 30-60 分鐘，分類 2-4 小時，標籤 1-2 小時

**快取服務層**
- `ArticleCacheService`: 文章內容快取管理
- `CategoryCacheService`: 分類資料快取管理  
- `TagCacheService`: 標籤資料快取管理
- 統一的 `cacheList()`, `cacheDetail()`, `clearCache()` 介面

## GCP 環境優化

### 記憶體限制配置

**Docker 容器配置**
```bash
redis-server --maxmemory 100mb --maxmemory-policy allkeys-lru
```

**記憶體管理策略**
- **容器限制**: 128MB Docker memory limit
- **Redis 限制**: 100MB maxmemory (避免 OOM)
- **淘汰策略**: allkeys-lru (最近最少使用)
- **持久化**: appendonly + everysec (平衡效能與安全)

## 監控與維護

### 效能監控策略

**關鍵監控指標**
- **記憶體使用率**: 監控接近 100MB 限制的情況
- **快取命中率**: 透過 `info stats` 監控 keyspace hits/misses
- **連接狀態**: Laravel 應用與 Redis 的連接穩定性
- **淘汰頻率**: allkeys-lru 策略的執行頻率

**維護最佳實踐**
- 定期檢查 Redis 記憶體使用情況
- 監控快取回退到 Database 的頻率
- 根據業務增長調整 TTL 策略
- 定期清理過期的快取標籤

---

*Redis 快取架構在 GCP e2-micro 環境下實現高效的記憶體管理和應用程式效能優化。*
