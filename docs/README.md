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

本專案使用 **Swagger/OpenAPI** 自動生成 API 文件，確保文件與代碼實作同步。

- **Swagger UI**: `/api/documentation` - 互動式 API 文件界面
- **OpenAPI JSON**: `/api/documentation.json` - API 規格文件
- **OpenAPI YAML**: 儲存於 `storage/api-docs/api-docs.yaml`

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