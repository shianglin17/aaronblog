# Aaron Blog 系統架構

## 1. 系統概述

個人部落格系統，採用前後端分離架構，後端提供 RESTful API，前端可支援多種客戶端。

### 1.1 技術選型

**後端**
- **框架**：Laravel 12 (PHP 8.2+)
- **資料庫**：MySQL 8.0 (開發) / SQLite (生產 GCP-VM)
- **認證**：Laravel Sanctum
- **快取**：Redis

**前端**
- **管理後台**：Vue.js 3 + TypeScript + Naive UI
- **前台**：Vue.js 3 + TypeScript

**部署**
- **容器化**：Docker & Docker Compose
- **雲端**：GCP-VM
- **Web 伺服器**：Nginx

## 2. 後端架構

### 2.1 分層架構

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

### 2.2 目錄結構

```
app/
├── Http/
│   ├── Controllers/     # 控制器
│   ├── Requests/        # 表單驗證
│   ├── Middleware/      # 中介軟體
│   └── Response/        # 回應處理
├── Services/            # 業務邏輯層
├── Repositories/        # 資料存取層
├── Models/              # Eloquent 模型
├── Transformer/         # 資料轉換器
├── Exceptions/          # 自定義例外
└── Providers/           # 服務提供者
```

### 2.3 核心模組

#### 2.3.1 認證模組
- **AuthController**：處理登入/登出
- **Laravel Sanctum**：API Token 管理

#### 2.3.2 文章模組
- **ArticleController**：文章 CRUD
- **ArticleService**：文章業務邏輯
- **ArticleRepository**：文章資料存取

#### 2.3.3 分類標籤模組
- **CategoryController** / **TagController**：分類標籤管理
- **CategoryService** / **TagService**：業務邏輯
- **CategoryRepository** / **TagRepository**：資料存取

#### 2.3.4 快取模組
- **ArticleCacheService**：文章快取管理
- **CategoryCacheService**：分類快取管理
- **TagCacheService**：標籤快取管理
- **快取驅動**：Redis (統一使用)

## 3. 資料架構

### 3.1 核心資料表

- **users**：用戶資料
- **articles**：文章內容
- **categories**：文章分類
- **tags**：文章標籤
- **article_tag**：文章標籤關聯

### 3.2 資料關聯

```
User (1) ←→ (N) Article
Article (N) ←→ (1) Category
Article (N) ←→ (M) Tag
```

## 4. API 設計

### 4.1 路由結構

```
/api/articles          # 公開文章 API
/api/categories        # 公開分類 API
/api/tags              # 公開標籤 API
/api/auth/*            # 認證相關 API
/api/admin/*           # 管理員專用 API
```

### 4.2 回應格式

統一使用 `ResponseMaker` 處理所有 API 回應：

```json
{
  "status": "success|error",
  "code": 200,
  "message": "訊息",
  "data": null
}
```

### 4.3 資料轉換

使用 `Transformer` 層統一處理資料格式化：
- `ArticleTransformer`
- `CategoryTransformer`
- `TagTransformer`

## 5. 安全策略

### 5.1 認證授權
- API Token 機制（Laravel Sanctum）
- 中介軟體保護敏感端點

### 5.2 資料驗證
- Form Request 驗證所有輸入
- XSS 防護
- SQL 注入防護（Eloquent ORM）

### 5.3 API 安全
- HTTPS 強制（生產環境）
- CORS 設定
- Rate Limiting

## 6. 效能優化

### 6.1 資料庫優化
- 適當的索引設計
- Eloquent 關聯預載入

### 6.2 快取策略
- **ArticleCacheService**：文章列表和詳情快取
- **CategoryCacheService**：分類資料快取
- **TagCacheService**：標籤資料快取
- **Redis 配置**：生產環境限制 100MB 記憶體，使用 LRU 淘汰策略
- **快取鍵管理**：統一的快取鍵命名規範

## 7. 部署架構

### 7.1 開發環境
```
Docker Compose (docker-compose.yml):
├── app (Laravel)
├── nginx (Nginx)
├── mysql (MySQL 8.0)
└── redis (Redis 7)
```

### 7.2 生產環境 (GCP-VM)
```
Docker Compose (docker-compose.gcp.yml):
├── app (Laravel + SQLite)
├── nginx (Nginx + SSL)
└── redis (Redis 7, 記憶體限制 100MB)

特色：
- SQLite 資料庫，節省資源
- 記憶體限制，適合免費層 VM
- 容器化部署，易於管理
```

## 8. 技術決策

### 8.1 為什麼選擇 Laravel？
- 成熟的 PHP 框架，生態完整
- 內建 Eloquent ORM，開發效率高
- Sanctum 認證簡單可靠

### 8.2 為什麼生產環境使用 SQLite？
- **資源節省**：適合 GCP 免費層 VM (1GB RAM)
- **部署簡單**：無需額外的資料庫服務
- **效能足夠**：個人部落格讀多寫少的場景
- **備份容易**：單一檔案，備份和遷移簡單

### 8.3 為什麼使用 Repository Pattern？
- 分離業務邏輯與資料存取
- 提高程式碼可測試性
- 便於未來擴展

### 8.3 為什麼使用 Transformer？
- 統一 API 回應格式
- 分離資料呈現邏輯
- 提高安全性（避免敏感資料外洩） 