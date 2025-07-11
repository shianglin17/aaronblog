# Aaron 部落格系統文件

本文件夾包含 Aaron 部落格系統的所有技術文件，包括系統架構、資料庫設計、API 文件等。

## 文件索引

### 專案概述

- **[專案作品集](project-portfolio.md)** - 完整的專案展示與技術亮點 ✅

### 系統架構

- **[系統架構文件](architecture/README.md)** - 完整的系統架構文檔 ✅
  - [系統概述](architecture/system-overview.md) - 系統架構概述與技術棧選型 ✅
  - [核心模組](architecture/modules/) - 認證、文章管理等核心模組架構 ✅
  - [部署架構](architecture/deployment/) - Docker、GCP 部署策略 ✅
  - [CI/CD 流程](architecture/cicd/) - GitHub Actions 自動化流程 ✅
  - [基礎設施](architecture/infrastructure/) - Redis、Nginx 等基礎設施配置 ✅
  - [程式碼架構](architecture/codebase/) - 目錄結構、設計模式、編碼規範 ✅
  - [資料庫設計](architecture/database.md) - 資料庫架構與設計 ✅

### 技術洞察

- **[技術決策與演進](technical-insights.md)** - 基於 Git 提交歷史的技術決策分析 ✅
  - 認證機制演進：從 Bearer Token 到 Session Cookie
  - 錯誤處理重構：Service 層異常處理架構
  - 快取策略設計：多層快取與 Redis Tags
  - Repository 模式：泛型設計與程式碼簡化

### 測試

- **[測試指南](testing-guide.md)** - 完整的測試策略與執行指南 ✅

### 版本控制

- **[版本管理規範](versioning.md)** - 語義化版本控制規範 ✅

### API 文件

- [API 總覽](api/README.md) - API 一覽表及規範 ✅

#### 認證相關 API

- [登入](api/auth/login.md) - 用戶登入 API ✅
- [登出](api/auth/logout.md) - 用戶登出 API ✅
- [用戶資訊](api/auth/user.md) - 獲取當前登入用戶資訊 API ✅
- [認證流程詳解](api/auth/auth-flow.md) - Laravel Sanctum 認證系統工作原理 ✅

#### 文章相關 API

- [文章列表](api/article/list.md) - 獲取文章列表 API ✅
- [文章詳情](api/article/show.md) - 獲取文章詳情 API ✅
- [建立文章](api/article/create.md) - 建立新文章 API ✅
- [更新文章](api/article/update.md) - 更新文章 API ✅
- [刪除文章](api/article/delete.md) - 刪除文章 API ✅

#### 分類相關 API

- [分類列表](api/category/list.md) - 獲取分類列表 API ✅
- [分類詳情](api/category/show.md) - 獲取分類詳情 API ✅
- [建立分類](api/category/create.md) - 建立新分類 API ✅
- [更新分類](api/category/update.md) - 更新分類 API ✅
- [刪除分類](api/category/delete.md) - 刪除分類 API ✅

#### 標籤相關 API

- [標籤列表](api/tag/list.md) - 獲取標籤列表 API ✅
- [標籤詳情](api/tag/show.md) - 獲取標籤詳情 API ✅
- [建立標籤](api/tag/create.md) - 建立新標籤 API ✅
- [更新標籤](api/tag/update.md) - 更新標籤 API ✅
- [刪除標籤](api/tag/delete.md) - 刪除標籤 API ✅

#### 通用設定

- [通用參數](api/common-parameters.md) - API 通用參數說明 ✅
- [錯誤處理](api/error-handling.md) - API 錯誤處理規範 ✅
- [分頁說明](api/pagination.md) - API 分頁機制說明 ✅
- [限流說明](api/rate-limiting.md) - API 限流機制說明 ✅

### Postman 集合

- [API 測試集合](postman/README.md) - Postman API 測試集合使用指南 ✅

## 項目開發狀態

| 模組 | 狀態 | 備註 |
|-----|------|------|
| 認證系統 | ✅ 已完成 | 使用 Laravel Sanctum 實作 |
| 文章管理 | ✅ 已完成 | 基本的 CRUD 功能已實作 |
| 分類標籤 | ✅ 已完成 | 文章分類與標籤功能已實作 |
| 前端界面 | ⏳ 進行中 | API 整合已完成，UI 開發中 |
| 評論系統 | 🔮 未來計劃 | 尚未開始實作 |

## 如何使用本文件

### 🎯 新手入門路徑
1. **系統理解**：閱讀 [系統架構概述](architecture/system-overview.md) 了解整體設計
2. **技術決策**：查看 [技術決策與演進](technical-insights.md) 了解技術選型背景
3. **核心模組**：研讀 [核心模組](architecture/modules/) 了解業務架構
4. **資料設計**：參考 [資料庫設計](architecture/database.md) 了解資料結構

### 🔧 開發者路徑
1. **程式碼架構**：重點關注 [程式碼架構](architecture/codebase/) 了解設計模式
2. **API 開發**：參考 [API 文件](api/README.md) 了解現有 API 設計規範
3. **測試策略**：查閱 [測試指南](testing-guide.md) 了解測試方法
4. **編碼規範**：遵循 [編碼規範](architecture/codebase/coding-standards.md)

### 🚀 運維人員路徑
1. **部署策略**：查看 [部署架構](architecture/deployment/) 了解部署流程
2. **基礎設施**：了解 [基礎設施](architecture/infrastructure/) 配置
3. **CI/CD 流程**：參考 [CI/CD 流程](architecture/cicd/) 自動化部署
4. **版本管理**：遵循 [版本管理規範](versioning.md) 進行發布

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

# 執行測試
php artisan test
```

## 文件結構說明

### 🗂️ 文件組織原則

1. **窮盡性**：涵蓋所有重要的技術面向
2. **非重複性**：避免內容重複，單一來源原則
3. **結構化**：按功能與用途清晰分類
4. **實用性**：反映實際專案實作內容

### 📋 文件維護

- 所有文件採用 Markdown 格式
- 文件更新應與代碼變更同步提交
- 重大架構或設計變更應先更新相關文件
- API 變更必須同步更新 API 文件和 Postman 集合

### 🔄 文件更新歷史

- 2025年：完成文件結構重構，移除重複內容
- 技術棧更新：統一使用 SQLite 資料庫
- 認證機制更新：遷移至 Session Cookie 認證
- 測試文件整合：合併為單一測試指南 