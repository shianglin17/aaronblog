# Aaron 部落格系統文件

本文件夾包含 Aaron 部落格系統的所有技術文件，包括系統架構、資料庫設計、API 文件等。

## 文件索引

### 產品文件

- [產品需求與規格](product/README.md) - 產品相關文件 ✅
  - [產品需求文件](product/prd.md) - 詳細的產品需求與功能規格 ✅

### 系統架構

- [系統架構文件](architecture/README.md) - 系統架構和技術選型的相關文件 ✅
  - [架構概述](architecture/architecture-overview.md) - 系統架構詳細說明 ✅

### 資料庫設計

- [資料庫設計](database/README.md) - 資料庫相關文件 ✅
  - [資料庫結構](database/database-schema.md) - 資料庫表結構與關聯設計 ✅
  - [資料遷移指南](database/migration-guide.md) - 資料庫遷移相關說明 *(待完成)*

### 前端開發

- [前端開發文件](frontend/README.md) - 前端相關文件 ✅
  - [Tailwind CSS 指南](frontend/tailwind-guide.md) - 前端樣式指南 ✅
  - [Vue.js 開發規範](frontend/vue-guidelines.md) - Vue.js 開發規範 *(待完成)*

### API 文件

- [API 總覽](api/README.md) - API 一覽表及規範 ✅

#### 認證相關 API

- [登入](api/auth/login.md) - 用戶登入 API ✅
- [登出](api/auth/logout.md) - 用戶登出 API ✅
- [用戶資訊](api/auth/user.md) - 獲取當前登入用戶資訊 API ✅
- [認證流程詳解](api/auth/auth-flow.md) - Laravel Sanctum 認證系統工作原理 ✅

#### 文章相關 API

- [文章列表](api/article/list.md) - 獲取文章列表 API ✅

#### 通用設定

- [通用規範](api/common/README.md) - API 通用規範與設定 ✅

### Postman 集合

- [API 測試集合](postman/aaron_blog_api.json) - Postman API 測試集合檔案 ✅

## 項目開發狀態

| 模組 | 狀態 | 備註 |
|-----|------|------|
| 認證系統 | ✅ 已完成 | 使用 Laravel Sanctum 實作 |
| 文章管理 | ✅ 已完成 | 基本的 CRUD 功能已實作 |
| 分類標籤 | ✅ 已完成 | 文章分類與標籤功能已實作 |
| 前端界面 | ⏳ 進行中 | API 整合已完成，UI 開發中 |
| 評論系統 | 🔮 未來計劃 | 尚未開始實作 |

## 如何使用本文件

1. 新開發者應先閱讀 [產品需求文件](product/prd.md) 了解系統目標和功能
2. 接著查看 [系統架構概述](architecture/architecture-overview.md) 了解整體架構設計
3. 開發 API 時，參考 [API 文件](api/README.md) 了解現有 API 設計和規範
4. 進行資料庫操作時，參考 [資料庫設計](database/database-schema.md)
5. 開發前端時，參考 [前端開發文件](frontend/README.md)

## 快速參考

### 常用指令

```bash
# 啟動開發服務器
php artisan serve

# 查看路由列表
php artisan route:list

# 查看資料庫狀態
php artisan db:show

# 執行資料庫遷移
php artisan migrate
```

## 文件貢獻指南

1. 所有文件採用 Markdown 格式
2. 文件更新應與代碼變更同步提交
3. 重大架構或設計變更應先更新相關文件
4. API 變更必須同步更新 API 文件和 Postman 集合 