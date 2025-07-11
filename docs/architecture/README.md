# 架構文檔總覽

## 文檔結構

本目錄包含 Aaron Blog 系統的完整架構文檔，按照不同層面進行組織：

### 📋 系統概述
- **[system-overview.md](system-overview.md)** - 系統架構概述，技術棧選型與整體設計

### 🧩 核心模組
- **[modules/authentication.md](modules/authentication.md)** - 認證模組架構
- **[modules/article-management.md](modules/article-management.md)** - 文章管理模組架構

### 🚀 部署架構
- **[deployment/](deployment/)** - 部署相關文檔（Docker、GCP 優化、部署策略）

### 🔄 CI/CD 流程
- **[cicd/](cicd/)** - 持續整合與部署文檔（GitHub Actions、發布流程）

### 🏗️ 基礎設施
- **[infrastructure/](infrastructure/)** - 基礎設施文檔（Redis、Nginx、Cloudflare）

### 💻 程式碼架構
- **[codebase/](codebase/)** - 程式碼架構文檔（目錄結構、設計模式、編碼規範）

### 🗄️ 資料庫設計
- **[database.md](database.md)** - 資料庫架構與設計（整合自 database/ 目錄）


## 技術洞察與演進

本專案的技術決策和架構演進過程詳細記錄在 [technical-insights.md](../technical-insights.md) 中，包含：

### 🎯 重要技術決策
- **認證機制演進**：從 Bearer Token 到 Session Cookie 的完整遷移過程
- **錯誤處理重構**：Service 層異常處理架構的建立
- **快取策略設計**：多層快取與 Redis Tags 的實作
- **Repository 模式**：泛型設計與程式碼簡化

### 📊 量化成果
- **程式碼減少**：BaseRepository 重構減少 275 行重複程式碼
- **架構優化**：12 個檔案的錯誤處理重構，提升可維護性
- **快取效能**：分層快取策略實現 3600s TTL 的高效快取

### 🔄 技術演進軌跡
1. **基礎建立**：CRUD + 軟刪除機制（後期已移除）
2. **架構重構**：Repository 模式 + 快取策略
3. **錯誤處理**：Exception 導向的錯誤處理架構
4. **認證優化**：Session Cookie 認證機制
5. **前端現代化**：Vue 3 + TypeScript 架構

## 閱讀建議

### 🎯 新手入門
1. 從 [system-overview.md](system-overview.md) 開始了解整體架構
2. 閱讀 [modules/](modules/) 了解核心業務模組
3. 查看 [database.md](database.md) 了解資料結構

### 🔧 開發者
1. 重點關注 [codebase/](codebase/) 程式碼架構
2. 參考 [modules/](modules/) 中的設計模式
3. 查閱 [../testing-guide.md](../testing-guide.md) 了解測試策略

### 🚀 運維人員
1. 查看 [deployment/](deployment/) 部署文檔
2. 了解 [infrastructure/](infrastructure/) 基礎設施配置
3. 參考 [cicd/](cicd/) 自動化流程

## 版本控制與發布

本專案採用語義化版本控制（Semantic Versioning），詳細規範請參考 [versioning.md](../versioning.md)。

### 📋 版本發布流程
- **MAJOR**：不相容的 API 修改（如資料庫結構重大變更）
- **MINOR**：新增功能且向下相容（如新增 API 端點）
- **PATCH**：向下相容的問題修正（如 Bug 修復）

### 🚀 發布命令
```bash
# 使用發布腳本
./scripts/release.sh patch   # 修訂版本
./scripts/release.sh minor   # 次版本
./scripts/release.sh major   # 主版本
```

## 文檔維護

- **更新頻率**：隨程式碼變更同步更新
- **版本控制**：與程式碼版本保持一致
- **審核流程**：架構變更需經過 PR 審核
- **技術洞察**：重要技術決策記錄在 technical-insights.md

---

*此架構文檔體系提供了完整的系統設計視角，從高層架構到具體實作細節，幫助團隊成員快速理解和維護系統。* 