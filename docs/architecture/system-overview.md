# Aaron Blog 系統架構概述

## 專案概述

Aaron Blog 是一個採用現代技術棧的全端個人部落格系統，已成功部署並運行於生產環境（[aaronlei.com](https://aaronlei.com/)）。此專案展現了從架構設計、程式開發到雲端部署的完整開發流程，並針對雲端資源成本進行了深度優化。

## 系統架構亮點

### 前後端分離架構
- **RESTful API 設計**：後端提供標準化 API，支援多端應用擴展
- **分層架構**：Controller → Service → Repository → Model，清晰的職責分工
- **設計模式**：Repository Pattern、Transformer Pattern、Exception Handling
- **程式碼品質**：完整的 E2E 測試、PHPDoc 文檔、程式碼規範

### 分層架構設計

```
┌─────────────────┐
│   HTTP Layer    │  ← Controllers, Requests, Middleware
├─────────────────┤
│  Service Layer  │  ← Business Logic
├─────────────────┤
│Repository Layer │  ← Data Access
├─────────────────┤
│   Model Layer   │  ← Eloquent Models
└─────────────────┘
```

## 技術棧選型

### 後端技術
- **框架**：Laravel 12（最新版本）+ PHP 8.2+
- **認證**：Laravel Sanctum Session Cookie 認證
- **資料庫**：SQLite 檔案型資料庫（開發與生產環境）
- **快取**：Redis 7 + 自定義快取服務層
- **API 設計**：RESTful API + 統一回應格式

### 前端技術
- **框架**：Vue 3 + Composition API + TypeScript
- **UI 套件**：Naive UI（現代化管理介面）
- **路由**：Vue Router 4
- **建構工具**：Vite（快速開發體驗）
- **功能**：Markdown 渲染、程式碼高亮、響應式設計

### DevOps & 部署
- **容器化**：Docker + Docker Compose
- **雲端平台**：Google Cloud Platform VM
- **Web 伺服器**：Nginx
- **CDN & 安全**：Cloudflare DNS + Proxy 代理
- **自動化**：腳本化部署流程、資源監控

## 核心模組架構

### 認證模組
- **AuthController**：處理登入/登出
- **Laravel Sanctum**：Session Cookie 認證機制
- **權限控制**：公開 API vs 管理員 API 分離

### 文章管理模組
- **ArticleController**：文章 CRUD 操作
- **ArticleService**：文章業務邏輯
- **ArticleRepository**：文章資料存取
- **ArticleTransformer**：資料格式化

### 分類標籤系統
- **CategoryController/TagController**：分類標籤管理
- **CategoryService/TagService**：業務邏輯處理
- **CategoryRepository/TagRepository**：資料存取層
- **關聯設計**：Article (N) ←→ (1) Category, Article (N) ←→ (M) Tag

### 快取系統
- **BaseCacheService**：抽象快取服務基類
- **ArticleCacheService**：文章快取管理
- **CategoryCacheService**：分類快取管理
- **TagCacheService**：標籤快取管理
- **Redis 配置**：生產環境限制 100MB 記憶體，使用 LRU 淘汰策略

## 資料架構

### 核心資料表
- **users**：用戶資料
- **articles**：文章內容（包含 Markdown 支援）
- **categories**：文章分類（SEO 友善 slug）
- **tags**：文章標籤
- **article_tag**：文章標籤關聯表

### 資料關聯
```
User (1) ←→ (N) Article
Article (N) ←→ (1) Category
Article (N) ←→ (M) Tag
```

## API 設計

### 路由結構
```
/api/articles          # 公開文章 API
/api/categories        # 公開分類 API
/api/tags              # 公開標籤 API
/api/auth/*            # 認證相關 API
/api/admin/*           # 管理員專用 API
```

### 統一回應格式
使用 `ResponseMaker` 處理所有 API 回應：

```json
{
  "status": "success|error",
  "code": 200,
  "message": "訊息",
  "data": null
}
```

## 部署架構

### 開發環境
```
Docker Compose (docker-compose.yml):
├── app (Laravel + SQLite)
├── nginx (Nginx)
└── redis (Redis 7)
```

### 生產環境 (GCP-VM)
```
Docker Compose (docker-compose.gcp.yml):
├── app (Laravel + SQLite)
├── nginx (Nginx + SSL)
└── redis (Redis 7, 記憶體限制 100MB)

優化特色：
- SQLite 資料庫，節省資源
- 記憶體限制，適合免費層 VM
- 容器化部署，易於管理
```

## 技術挑戰與解決方案

### 雲端成本優化
**挑戰**：在 GCP 免費層 VM（1GB RAM）上運行完整應用
**解決方案**：
- 整合統一使用 SQLite 檔案型資料庫，節省記憶體
- Redis 記憶體限制設定（100MB + LRU 淘汰策略）
- Docker 資源配置優化

### 認證機制演進
**挑戰**：選擇適合的認證方案
**解決方案**：
- 從 API Token 遷移到 Session Cookie 認證
- 前後端一致的認證狀態管理
- CSRF 保護與錯誤處理

### 快取策略設計
**挑戰**：設計高效的快取策略提升 API 回應速度
**解決方案**：
- 實作 `BaseCacheService` 抽象類別
- 各模組獨立的快取服務
- TTL 策略 + Cache Tags 管理

## 效能優化

### 資料庫優化
- 適當的索引設計
- Eloquent 關聯預載入
- 查詢優化與 select 指定欄位

### 快取策略
- **多層快取**：列表快取（24小時）+ 詳情快取（3天）
- **Cache Tags**：精確的快取清除機制
- **參數標準化**：搜尋、排序、分頁參數的簽名生成

### 前端優化
- 程式碼分割與懶加載
- 資源壓縮與 CDN 加速
- 響應式設計與使用者體驗

## 安全策略

### 認證授權
- Session Cookie 機制（Laravel Sanctum）
- 中介軟體保護敏感端點
- CSRF 保護

### 資料驗證
- Form Request 驗證所有輸入
- XSS 防護
- SQL 注入防護（Eloquent ORM）

### API 安全
- HTTPS 強制（生產環境）
- CORS 設定
- Rate Limiting
- Cloudflare 防護

## 專案成果

- **成功上線**：穩定運行於生產環境
- **成本控制**：在免費雲端資源上實現完整功能
- **程式碼品質**：完整的文檔與 E2E 測試
- **可維護性**：清晰的架構與程式碼規範
- **擴展性**：模組化設計，易於功能擴展

---

*此系統架構展現了從需求分析、架構設計到部署優化的完整軟體開發生命週期，特別著重於現代化技術應用與雲端資源優化。* 