# Aaron Blog 系統架構

## 專案概述

Aaron Blog 是現代化的個人部落格系統，成功部署於 [aaronlei.com](https://aaronlei.com/)。針對 GCP e2-micro 免費資源進行深度優化，實現高效的記憶體管理和效能表現。

## 🏗️ 核心架構

### 技術棧
- **後端**: Laravel 12 + PHP 8.2
- **前端**: Vue 3 + TypeScript + Naive UI
- **資料庫**: SQLite (生產環境)
- **快取**: Redis
- **認證**: Laravel Sanctum (Session Cookie)
- **容器化**: Docker
- **部署**: GCP e2-micro VM
- **CI/CD**: GitHub Actions

### 分層架構
```
┌─────────────────┐
│   HTTP Layer    │  ← Controllers, Requests, Middleware
├─────────────────┤
│  Service Layer  │  ← Business Logic, Cache Services  
├─────────────────┤
│Repository Layer │  ← Data Access, Query Optimization
├─────────────────┤
│   Model Layer   │  ← Eloquent Models, Relations
└─────────────────┘
```

## 📊 資料庫設計

### 核心實體關聯
```
┌───────────┐      ┌───────────┐      ┌───────────┐
│   users   │──1:n─▶  articles ◀─n:1──│ categories│
└───────────┘      └─────┬─────┘      └───────────┘
                         │ n:m
                         ▼
                   ┌───────────┐
                   │    tags   │
                   └───────────┘
```

### 關鍵設計決策
- **SQLite**: 針對個人部落格場景，節省記憶體和成本
- **無軟刪除**: 簡化查詢邏輯，提升效能
- **slug 唯一性**: 支援 SEO 友善的 URL 結構

## 🚀 部署架構

### 生產環境配置
- **平台**: Google Cloud Platform e2-micro (免費方案)
- **資源限制**: 1 vCPU, 1GB RAM
- **容器化**: Docker + docker-compose
- **反向代理**: Nginx
- **SSL**: Let's Encrypt (Cloudflare)

### 記憶體優化分配
```
應用程式:      ~450MB
SQLite:        ~50MB  
Redis:         ~80MB
系統保留:      ~420MB
總計:          1GB (100% 利用率)
```

## ⚡ Redis 快取策略

### 資料庫分離架構
```
┌─────────────────────────────┐
│        Redis Cache          │
├─────────────────────────────┤
│  Database 0: 預設           │
│  ├── Session Storage       │
│  ├── Queue Jobs            │
│  └── General Purpose       │
├─────────────────────────────┤
│  Database 1: 應用快取        │
│  ├── Article Cache         │
│  ├── Category Cache        │
│  ├── Tag Cache             │
│  └── API Response Cache    │
└─────────────────────────────┘
```

### TTL 分層策略
- **文章快取**: 列表 30 分鐘, 詳情 60 分鐘
- **分類快取**: 列表 2 小時, 詳情 4 小時  
- **標籤快取**: 列表 1 小時, 詳情 2 小時

### 標籤式快取管理
- **分類標籤**: `article:list`, `article:detail`, `category:list`
- **精確清理**: 使用 Redis Tags 進行批量快取失效
- **關聯清理**: 文章更新時自動清理相關分類、標籤快取

### 記憶體優化配置
- **記憶體限制**: 100MB (maxmemory)
- **淘汰策略**: allkeys-lru (最近最少使用)
- **持久化**: appendonly + everysec

## 🔧 核心設計模式

### Repository Pattern
- **BaseRepository**: 泛型 CRUD 操作，大幅減少重複代碼
- **ArticleRepository**: 精確查詢與關聯載入優化
- **程式碼簡化**: CategoryRepository、TagRepository 僅需 3 行代碼

### API 回應架構
- **統一格式**: ApiResponse::ok(), ApiResponse::paginated()
- **異常處理**: ApiException 基類 + 具體異常類別
- **全域處理**: 自動轉換異常為標準 API 回應

### 快取服務架構
- **BaseCacheService**: 模板方法模式，統一快取操作
- **服務分層**: 每個業務實體有專門的快取服務
- **標準化**: cacheList(), cacheDetail(), clearCache() 統一介面

## 🔐 安全架構

### 認證機制
- **Session Cookie**: HttpOnly + SameSite=Lax 防護
- **CSRF 保護**: 所有 CUD 操作必須提供 CSRF Token
- **權限控制**: AdminOnly 中介軟體保護後台 API

### API 安全
- **速率限制**: 每分鐘 30 次請求
- **輸入驗證**: Laravel FormRequest 嚴格驗證
- **錯誤處理**: 統一錯誤回應，避免資訊洩漏

## 📱 前後台分離架構

### 前台 (公開 API)
- **路由**: `/api/articles`, `/api/categories`, `/api/tags`
- **資料範圍**: 僅已發布內容，支援 SEO
- **快取策略**: 長時間快取，重視效能

### 後台 (管理 API)
- **路由**: `/api/admin/*`
- **認證保護**: Laravel Sanctum Session 認證
- **功能**: 完整 CRUD，包含草稿管理
- **快取策略**: 短時間快取，重視即時性

## 🔄 CI/CD 自動化流程

### GitHub Actions 流水線
```
推送到 main → 自動測試 → 並行構建 → 自動部署 → 狀態通知
            ├── SQLite + Redis 測試環境
            ├── 前端構建 (npm cache)
            ├── Docker 構建 (layer cache)
            └── GCP 部署 (自動回滾)
```

### 自動化特性
- **測試環境**: 與生產環境一致的 SQLite + Redis
- **構建優化**: npm cache + Docker layer cache
- **部署安全**: 自動備份 + 失敗回滾
- **狀態監控**: 部署成功/失敗自動通知

## 📈 效能優化策略

### 查詢優化
- **精確查詢**: 只選擇需要的欄位，避免冗余
- **關聯預載入**: with() 避免 N+1 查詢問題
- **索引策略**: 基於實際查詢模式設計

### 前端優化
- **程式碼分割**: Vite 自動 Code Splitting
- **懶載入**: 路由層級組件懶載入
- **快取策略**: 瀏覽器快取 + CDN 加速

### 快取效能
- **命中率**: > 90% (文章列表、分類標籤)
- **效能提升**: 首頁載入從 480ms → 120ms (75% 改善)
- **預熱策略**: 應用啟動時預熱熱門內容

## 🔍 監控與維護

### 關鍵效能指標
- **API 回應時間**: < 100ms
- **頁面載入時間**: < 2s
- **記憶體使用率**: < 85% (850MB)
- **快取命中率**: > 90%
- **資料庫大小**: < 50MB

### 自動化維護
- **部署流程**: GitHub Actions 全自動
- **資料備份**: 每日自動備份 SQLite
- **資源清理**: 定期清理 Docker 映像
- **健康檢查**: Redis 連線、記憶體監控

## 📚 相關文件

### 詳細技術決策
重要決策的背景和原因請參考 [ADR 文件](../adr/README.md)：
- [ADR-001](../adr/001-sqlite-production-database.md) - SQLite 資料庫選擇
- [ADR-002](../adr/002-redis-caching-strategy.md) - Redis 快取策略
- [ADR-003](../adr/003-session-csrf-authentication.md) - 認證機制設計
- [ADR-005](../adr/005-frontend-backend-separation.md) - 前後台分離
- [ADR-006](../adr/006-github-actions-cicd.md) - CI/CD 自動化

### API 規格文件
- **Swagger UI**: `/api/documentation` - 互動式 API 文件
- **OpenAPI JSON**: `/api/documentation.json` - 機器可讀規格

### 詳細設計文件
- **[系統概述](system-overview.md)** - 完整的系統架構說明
- **[資料庫設計](database.md)** - 資料表結構與關聯設計 