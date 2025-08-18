# ADR-005: 前後台邏輯完全分離

## Status
Accepted - 2025年8月13日

## Context

隨著部落格系統功能增長，前台公開瀏覽與後台管理功能混雜在同一套邏輯中，導致代碼耦合和維護困難。

### 架構問題
- **邏輯混合**: 前台和後台使用相同的 API 端點和資料處理邏輯
- **權限混亂**: 公開資料和管理資料在同一個 Composable 中處理
- **狀態衝突**: 前後台的資料狀態互相影響，導致意外的資料更新
- **測試困難**: 混合邏輯增加測試的複雜度

### 業務需求差異
- **前台**: 只需要已發布內容，重視 SEO 和讀取效能
- **後台**: 需要完整內容管理，包含草稿、編輯、刪除功能
- **資料範圍**: 前台按權限篩選，後台顯示用戶所有內容

**相關 Commit**: `20620ae` - "refactor: 完全分離前後台文章管理邏輯，解決API耦合問題"

## Decision

實作 **完全分離的前後台邏輯架構**，為前台和後台建立獨立的 API 端點、Composable 和資料管理邏輯。

### 分離策略

1. **API 端點分離**: 前台使用 `/api/articles`，後台使用 `/api/admin/articles`
2. **Composable 分離**: 建立專用的管理後台 Composable
3. **資料狀態分離**: 前後台維護獨立的資料狀態
4. **業務邏輯分離**: 權限、篩選、操作邏輯完全分開

## Consequences

### 正面影響 ✅

- **清晰職責**: 前後台邏輯各司其職，職責明確
- **獨立演進**: 前後台功能可以獨立開發和優化
- **狀態隔離**: 消除資料狀態衝突問題
- **易於測試**: 分離的邏輯更容易單元測試
- **權限安全**: 前後台權限控制更加明確

### 負面影響 ❌

- **代碼重複**: 某些通用邏輯可能在兩邊都需要實作
- **維護成本**: 需要同時維護兩套邏輯
- **學習曲線**: 開發者需要了解兩套不同的 API

### 複雜度控制

通過基礎類別和共用工具減少重複：
```typescript
// 共用基礎邏輯
class BaseArticleComposable {
  // 通用的資料處理邏輯
}

// 前台專用
class PublicArticleComposable extends BaseArticleComposable {}

// 後台專用  
class AdminArticleComposable extends BaseArticleComposable {}
```

## Implementation

### API 端點分離

#### 前台 API (`/api/articles`)
```php
// 只返回已發布的文章，支持 SEO 優化
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
```

#### 後台 API (`/api/admin/articles`)  
```php
// 返回當前用戶的所有文章，包含草稿
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/articles', [AdminArticleController::class, 'index']);
    Route::post('/admin/articles', [AdminArticleController::class, 'store']);
    Route::put('/admin/articles/{id}', [AdminArticleController::class, 'update']);
    Route::delete('/admin/articles/{id}', [AdminArticleController::class, 'destroy']);
});
```

### Composable 架構重構

#### 新增管理後台專用 Composables

```typescript
// composables/useAdminArticle.ts
export const useAdminArticles = () => {
  // 使用管理 API 進行文章列表管理
  // 包含草稿和已發布狀態
};

export const useAdminArticleForm = () => {
  // 管理後台文章表單邏輯
  // 支持創建、編輯、狀態切換
};

export const useAdminArticleDelete = () => {
  // 管理後台文章刪除邏輯
  // 包含確認對話框和樂觀更新
};

export const useAdminOptions = () => {
  // 管理後台選項數據管理
  // 分類、標籤等選項資料
};
```

#### 保持前台 Composables 簡潔

```typescript
// composables/useArticle.ts - 前台專用
export const useArticles = () => {
  // 只處理已發布文章
  // 重視 SEO 和載入效能
};
```

### 頁面組件更新

#### 管理後台頁面重構
```vue
<!-- pages/admin/ArticleManagement.vue -->
<script setup>
import { 
  useAdminArticles,
  useAdminArticleForm, 
  useAdminArticleDelete,
  useAdminOptions 
} from '@/composables/useAdminArticle';

// 使用專門的管理後台邏輯
const { articles, loading, fetchArticles } = useAdminArticles();
const { form, submitForm } = useAdminArticleForm();
const { deleteArticle } = useAdminArticleDelete();
const { categories, tags } = useAdminOptions();
</script>
```

### 狀態管理分離

```typescript
// 前台狀態 - stores/articles.ts
export const useArticleStore = defineStore('articles', () => {
  // 只管理公開文章狀態
});

// 後台狀態 - stores/admin.ts  
export const useAdminStore = defineStore('admin', () => {
  // 管理後台相關狀態
});
```

## 資料流程設計

### 前台資料流
```
用戶瀏覽 → useArticles() → /api/articles → 已發布文章 → SEO 優化展示
```

### 後台資料流
```
管理員操作 → useAdminArticles() → /api/admin/articles → 所有文章 → 管理界面
```

## 權限控制策略

### 前台權限
- 無需認證
- 只能存取已發布內容
- 自動 SEO 優化

### 後台權限  
- 需要 Sanctum 認證
- 只能存取自己的內容
- 完整 CRUD 操作權限

## 效能優化

### 快取策略分離
```php
// 前台快取 - 長時間快取
Cache::tags(['public', 'articles'])->remember('articles:public', 3600);

// 後台快取 - 短時間快取
Cache::tags(['admin', 'articles'])->remember('articles:admin:user:'.$userId, 300);
```

### 資料庫查詢優化
```php
// 前台查詢 - 只查詢已發布
Article::where('status', 'published')->latest();

// 後台查詢 - 查詢用戶所有文章
Article::where('user_id', auth()->id())->latest();
```

## 測試策略

### 分離測試檔案
```php
// tests/Feature/PublicArticleTest.php - 前台 API 測試
// tests/Feature/AdminArticleTest.php - 後台 API 測試
// tests/Unit/AdminArticleComposableTest.js - 後台邏輯測試
```

## 相關決策

- 依賴 [ADR-003 認證機制](003-session-csrf-authentication.md) 提供後台安全認證
- 利用 [ADR-004 Swagger 文件](004-swagger-api-documentation.md) 清楚區分前後台 API
- 配合 [ADR-002 Redis 快取](002-redis-caching-strategy.md) 實現分離的快取策略