# 專案目錄結構

## 概述

Aaron Blog 採用標準的 Laravel 專案結構，結合 Vue.js 前端應用程式。專案結構清晰地分離了後端邏輯、前端介面、配置檔案和文檔等不同職責的程式碼。

## 整體目錄結構

```
aaronblog/
├── app/                        # Laravel 應用程式核心
│   ├── Http/                   # HTTP 層
│   │   ├── Controllers/        # 控制器
│   │   ├── Middleware/         # 中介軟體
│   │   ├── Requests/           # 表單請求驗證
│   │   └── Resources/          # API 資源轉換
│   ├── Models/                 # Eloquent 模型
│   ├── Services/               # 業務邏輯服務
│   ├── Repositories/           # 資料存取層
│   └── Exceptions/             # 自定義例外
├── resources/                  # 前端資源
│   ├── js/                     # Vue.js 應用程式
│   │   ├── components/         # Vue 元件
│   │   ├── pages/              # 頁面元件
│   │   ├── api/                # API 呼叫
│   │   ├── router/             # 路由配置
│   │   └── types/              # TypeScript 類型定義
│   ├── css/                    # 樣式檔案
│   └── views/                  # Blade 模板
├── database/                   # 資料庫相關
│   ├── migrations/             # 資料庫遷移
│   ├── seeders/                # 資料填充
│   └── factories/              # 模型工廠
├── tests/                      # 測試檔案
│   ├── Feature/                # 功能測試
│   └── Unit/                   # 單元測試
├── config/                     # 配置檔案
├── routes/                     # 路由定義
├── storage/                    # 儲存目錄
├── public/                     # 公開檔案
├── docs/                       # 專案文檔
└── docker/                     # Docker 配置
```

## 後端架構 (Laravel)

### app/ 目錄結構

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php              # 基礎控制器
│   │   ├── AuthController.php          # 認證控制器
│   │   ├── ArticleController.php       # 文章控制器
│   │   ├── CategoryController.php      # 分類控制器
│   │   └── TagController.php           # 標籤控制器
│   ├── Middleware/
│   │   ├── Authenticate.php            # 認證中介軟體
│   │   ├── EncryptCookies.php          # Cookie 加密
│   │   └── VerifyCsrfToken.php         # CSRF 驗證
│   ├── Requests/
│   │   ├── LoginRequest.php            # 登入請求驗證
│   │   ├── ArticleRequest.php          # 文章請求驗證
│   │   ├── CategoryRequest.php         # 分類請求驗證
│   │   └── TagRequest.php              # 標籤請求驗證
│   └── Resources/
│       ├── ArticleResource.php         # 文章資源轉換
│       ├── CategoryResource.php        # 分類資源轉換
│       └── TagResource.php             # 標籤資源轉換
├── Models/
│   ├── User.php                        # 使用者模型
│   ├── Article.php                     # 文章模型
│   ├── Category.php                    # 分類模型
│   └── Tag.php                         # 標籤模型
├── Services/
│   ├── ArticleService.php              # 文章業務邏輯
│   ├── CategoryService.php             # 分類業務邏輯
│   ├── TagService.php                  # 標籤業務邏輯
│   └── CacheService.php                # 快取服務
├── Repositories/
│   ├── ArticleRepository.php           # 文章資料存取
│   ├── CategoryRepository.php          # 分類資料存取
│   └── TagRepository.php               # 標籤資料存取
└── Exceptions/
    ├── Handler.php                     # 例外處理器
    └── CustomException.php             # 自定義例外
```

### 分層架構說明

**1. Controller 層**
- 負責處理 HTTP 請求
- 呼叫 Service 層處理業務邏輯
- 返回 JSON 回應

**2. Service 層**
- 包含核心業務邏輯
- 呼叫 Repository 層存取資料
- 處理複雜的業務規則

**3. Repository 層**
- 負責資料存取邏輯
- 封裝資料庫查詢
- 提供統一的資料介面

**4. Model 層**
- Eloquent ORM 模型
- 定義資料表關聯
- 包含資料驗證規則

## 前端架構 (Vue.js)

### resources/js/ 目錄結構

```
resources/js/
├── components/                 # 可重用元件
│   ├── common/                 # 通用元件
│   │   ├── AppHeader.vue       # 頁首元件
│   │   ├── AppFooter.vue       # 頁尾元件
│   │   ├── LoadingSpinner.vue  # 載入動畫
│   │   └── Pagination.vue      # 分頁元件
│   ├── article/                # 文章相關元件
│   │   ├── ArticleCard.vue     # 文章卡片
│   │   ├── ArticleList.vue     # 文章列表
│   │   └── ArticleForm.vue     # 文章表單
│   ├── category/               # 分類相關元件
│   │   ├── CategoryList.vue    # 分類列表
│   │   └── CategoryForm.vue    # 分類表單
│   └── tag/                    # 標籤相關元件
│       ├── TagList.vue         # 標籤列表
│       └── TagForm.vue         # 標籤表單
├── pages/                      # 頁面元件
│   ├── Home.vue                # 首頁
│   ├── ArticleDetail.vue       # 文章詳情頁
│   ├── Login.vue               # 登入頁
│   └── admin/                  # 管理介面
│       ├── ArticleManagement.vue   # 文章管理
│       ├── CategoryManagement.vue  # 分類管理
│       └── TagManagement.vue       # 標籤管理
├── api/                        # API 呼叫
│   ├── index.ts                # API 統一入口
│   ├── auth.ts                 # 認證 API
│   ├── article.ts              # 文章 API
│   ├── category.ts             # 分類 API
│   ├── tag.ts                  # 標籤 API
│   └── http.ts                 # HTTP 客戶端配置
├── router/                     # 路由配置
│   └── index.ts                # 路由定義
├── stores/                     # Pinia 狀態管理
│   ├── auth.ts                 # 認證狀態
│   ├── article.ts              # 文章狀態
│   └── ui.ts                   # UI 狀態
├── composables/                # 組合式函數
│   ├── useAuth.ts              # 認證相關
│   ├── useApi.ts               # API 相關
│   └── useLoading.ts           # 載入狀態
├── types/                      # TypeScript 類型定義
│   ├── api.ts                  # API 類型
│   ├── article.ts              # 文章類型
│   ├── category.ts             # 分類類型
│   └── tag.ts                  # 標籤類型
├── utils/                      # 工具函數
│   ├── helpers.ts              # 輔助函數
│   ├── constants.ts            # 常數定義
│   └── validators.ts           # 驗證函數
├── styles/                     # 樣式檔案
│   ├── main.css                # 主要樣式
│   ├── components.css          # 元件樣式
│   └── utilities.css           # 工具樣式
├── app.ts                      # Vue 應用程式入口
└── bootstrap.ts                # 應用程式初始化
```

### 前端架構說明

**1. 元件化設計**
- 按功能模組組織元件
- 可重用的通用元件
- 頁面級別的路由元件

**2. API 層**
- 統一的 API 呼叫介面
- 請求/回應攔截器
- 錯誤處理機制

**3. 狀態管理**
- 使用 Pinia 管理應用狀態
- 模組化的狀態結構
- 響應式資料更新

**4. 路由管理**
- Vue Router 路由配置
- 路由守衛處理認證
- 動態路由載入

## 資料庫結構

### database/ 目錄結構

```
database/
├── migrations/                 # 資料庫遷移
│   ├── 2024_01_01_000000_create_users_table.php
│   ├── 2024_01_02_000000_create_categories_table.php
│   ├── 2024_01_03_000000_create_tags_table.php
│   ├── 2024_01_04_000000_create_articles_table.php
│   └── 2024_01_05_000000_create_article_tag_table.php
├── seeders/                    # 資料填充
│   ├── DatabaseSeeder.php      # 主要填充器
│   ├── UserSeeder.php          # 使用者資料
│   ├── CategorySeeder.php      # 分類資料
│   ├── TagSeeder.php           # 標籤資料
│   └── ArticleSeeder.php       # 文章資料
└── factories/                  # 模型工廠
    ├── UserFactory.php         # 使用者工廠
    ├── CategoryFactory.php     # 分類工廠
    ├── TagFactory.php          # 標籤工廠
    └── ArticleFactory.php      # 文章工廠
```

### 資料表關係

```
┌─────────────┐    ┌─────────────────┐    ┌─────────────┐
│    users    │    │    articles     │    │ categories  │
├─────────────┤    ├─────────────────┤    ├─────────────┤
│ id          │    │ id              │    │ id          │
│ name        │    │ title           │    │ name        │
│ email       │    │ slug            │    │ slug        │
│ password    │    │ content         │    │ description │
│ created_at  │    │ excerpt         │    │ created_at  │
│ updated_at  │    │ published_at    │    │ updated_at  │
└─────────────┘    │ category_id  ───┼────┤             │
                   │ created_at      │    └─────────────┘
                   │ updated_at      │
                   └─────────────────┘
                            │
                            │ Many-to-Many
                            │
                   ┌─────────────────┐    ┌─────────────┐
                   │   article_tag   │    │    tags     │
                   ├─────────────────┤    ├─────────────┤
                   │ article_id   ───┼────┤ id          │
                   │ tag_id       ───┼────┤ name        │
                   │ created_at      │    │ slug        │
                   │ updated_at      │    │ description │
                   └─────────────────┘    │ created_at  │
                                          │ updated_at  │
                                          └─────────────┘
```

## 測試結構

### tests/ 目錄結構

```
tests/
├── Feature/                    # 功能測試
│   ├── AuthTest.php            # 認證功能測試
│   ├── ArticleTest.php         # 文章功能測試
│   ├── CategoryTest.php        # 分類功能測試
│   └── TagTest.php             # 標籤功能測試
├── Unit/                       # 單元測試
│   ├── Models/                 # 模型測試
│   │   ├── UserTest.php        # 使用者模型測試
│   │   ├── ArticleTest.php     # 文章模型測試
│   │   ├── CategoryTest.php    # 分類模型測試
│   │   └── TagTest.php         # 標籤模型測試
│   ├── Services/               # 服務測試
│   │   ├── ArticleServiceTest.php  # 文章服務測試
│   │   ├── CategoryServiceTest.php # 分類服務測試
│   │   └── TagServiceTest.php      # 標籤服務測試
│   └── Repositories/           # 資料存取測試
│       ├── ArticleRepositoryTest.php   # 文章資料存取測試
│       ├── CategoryRepositoryTest.php  # 分類資料存取測試
│       └── TagRepositoryTest.php       # 標籤資料存取測試
├── CreatesApplication.php      # 測試應用程式建立
└── TestCase.php                # 測試基礎類別
```

### 測試策略

**1. 功能測試 (Feature Tests)**
- 測試完整的 HTTP 請求流程
- 驗證 API 端點的正確性
- 包含認證和授權測試

**2. 單元測試 (Unit Tests)**
- 測試個別類別和方法
- 模擬外部依賴
- 確保程式碼邏輯正確

**3. 測試資料**
- 使用工廠模式產生測試資料
- 獨立的測試資料庫
- 每次測試後清理資料

## 配置檔案

### config/ 目錄結構

```
config/
├── app.php                     # 應用程式基本配置
├── auth.php                    # 認證配置
├── cache.php                   # 快取配置
├── database.php                # 資料庫配置
├── filesystems.php             # 檔案系統配置
├── logging.php                 # 日誌配置
├── mail.php                    # 郵件配置
├── queue.php                   # 佇列配置
├── sanctum.php                 # Sanctum 認證配置
├── services.php                # 第三方服務配置
└── session.php                 # Session 配置
```

### 環境配置

```
.env                            # 環境變數配置
.env.example                    # 環境變數範例
.env.testing                    # 測試環境配置
```

## 路由結構

### routes/ 目錄結構

```
routes/
├── api.php                     # API 路由
├── web.php                     # Web 路由
├── console.php                 # 命令列路由
└── channels.php                # 廣播頻道路由
```

### API 路由組織

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // 認證路由
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 公開路由
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/tags', [TagController::class, 'index']);
    
    // 需要認證的路由
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('articles', ArticleController::class)
            ->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)
            ->except(['index', 'show']);
        Route::apiResource('tags', TagController::class)
            ->except(['index', 'show']);
    });
});
```

## 部署相關

### Docker 配置

```
docker/
├── nginx/                      # Nginx 配置
│   ├── Dockerfile              # Nginx 容器
│   └── default.conf            # Nginx 配置檔
├── php/                        # PHP 配置
│   └── Dockerfile              # PHP 容器
└── docker-compose.yml          # 容器編排
```

### 腳本檔案

```
scripts/
├── deploy.sh                   # 部署腳本
├── release.sh                  # 發布腳本
└── backup.sh                   # 備份腳本
```

## 文檔結構

### docs/ 目錄結構

```
docs/
├── architecture/               # 架構文檔
│   ├── system-overview.md      # 系統概述
│   ├── modules/                # 模組文檔
│   ├── deployment/             # 部署文檔
│   ├── cicd/                   # CI/CD 文檔
│   ├── infrastructure/         # 基礎設施文檔
│   └── codebase/               # 程式碼文檔
├── api/                        # API 文檔
│   ├── authentication.md       # 認證 API
│   ├── articles.md             # 文章 API
│   ├── categories.md           # 分類 API
│   └── tags.md                 # 標籤 API
├── development/                # 開發文檔
│   ├── setup.md                # 環境設定
│   ├── testing.md              # 測試指南
│   └── deployment.md           # 部署指南
└── technical-insights.md       # 技術見解
```

## 最佳實踐

### 目錄組織原則

1. **按功能模組組織**
   - 相關的檔案放在同一目錄
   - 清晰的職責分離
   - 易於維護和擴展

2. **分層架構**
   - Controller → Service → Repository → Model
   - 每層有明確的職責
   - 降低耦合度

3. **命名規範**
   - 使用描述性的檔案名稱
   - 遵循 Laravel 和 Vue.js 的命名慣例
   - 保持一致的命名風格

### 檔案組織建議

1. **保持目錄結構簡潔**
   - 避免過深的巢狀結構
   - 相關檔案放在一起
   - 使用描述性的目錄名稱

2. **分離關注點**
   - 前端和後端程式碼分離
   - 配置和程式碼分離
   - 測試和業務邏輯分離

3. **文檔化**
   - 重要的目錄要有 README
   - 複雜的邏輯要有註釋
   - 保持文檔更新

---

*此目錄結構設計確保了 Aaron Blog 專案的可維護性、可擴展性和團隊協作效率。* 