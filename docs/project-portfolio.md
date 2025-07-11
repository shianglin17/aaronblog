# AaronBlog - 全端個人部落格系統

## 專案概述

一個採用現代技術棧的全端部落格系統，已成功部署並運行於生產環境（[aaronlei.com](https://aaronlei.com/)）。此專案展現了從架構設計、程式開發到雲端部署的完整開發流程，並針對雲端資源成本進行了深度優化。

## 技術亮點

### 系統架構
- **前後端分離**：RESTful API 設計，支援多端應用擴展
- **分層架構**：Controller → Service → Repository → Model，清晰的職責分工
- **設計模式**：Repository Pattern、Transformer Pattern、Exception Handling
- **程式碼品質**：E2E 測試、PHPDoc 文檔、程式碼規範

### 技術棧

**後端技術**
- **框架**：Laravel 12（最新版本）+ PHP 8.2+
- **認證**：Laravel Sanctum Session Cookie 認證
- **資料庫**：SQLite 檔案型資料庫（資源優化）
- **快取**：Redis 7 + 自定義快取服務層
- **API 設計**：RESTful API + 統一回應格式

**前端技術**
- **框架**：Vue 3 + Composition API + TypeScript
- **UI 套件**：Naive UI（現代化管理介面）
- **路由**：Vue Router 4
- **建構工具**：Vite（快速開發體驗）
- **功能**：Markdown 渲染、程式碼高亮、響應式設計

**DevOps & 部署**
- **容器化**：Docker + Docker Compose
- **雲端平台**：Google Cloud Platform VM
- **Web 伺服器**：Nginx
- **CDN & 安全**：Cloudflare DNS + Proxy 代理
- **自動化**：腳本化部署流程、資源監控

## 核心功能

### 內容管理系統
- **文章管理**：Markdown 編輯、分類標籤、狀態管理
- **分類系統**：層級分類、SEO 友善 URL（slug）
- **標籤系統**：多對多關聯、靈活標記
- **搜尋功能**：全文搜尋、條件篩選

### 認證與權限
- **API 認證**：基於 Token 的無狀態認證
- **權限控制**：公開 API vs 管理員 API 分離
- **安全防護**：XSS 防護、SQL 注入防護、Cloudflare 防護

### 效能優化
- **多層快取**：Redis 快取 + 自定義快取服務
- **CDN 加速**：Cloudflare CDN 靜態資源快取
- **資料庫優化**：索引設計、關聯預載入、查詢優化
- **前端優化**：程式碼分割、懶加載、資源壓縮

## 技術挑戰與解決方案

### 雲端成本優化
**挑戰**：在 GCP 免費層 VM（1GB RAM）上運行完整應用
**解決方案**：
- 生產環境採用 SQLite 檔案型資料庫，節省記憶體
- Redis 記憶體限制設定（100MB + LRU 淘汰策略）
- Docker 資源配置優化

### 快取策略設計
**挑戰**：設計高效的快取策略提升 API 回應速度
**解決方案**：
- 實作 `BaseCacheService` 抽象類別
- 各模組獨立的快取服務（Article, Category, Tag）
- TTL 策略 + Cache Tags 管理

### 現代化前端架構
**挑戰**：打造高品質的管理後台與用戶介面
**解決方案**：
- Vue 3 Composition API + TypeScript 強型別
- 可重用的組件設計（DataTable, FormModal, FilterBar）
- 統一的 API 封裝與錯誤處理

## 專案管理與開發流程

### 文檔化
- **API 文檔**：完整的 OpenAPI 規範
- **架構文檔**：系統設計說明與技術決策
- **資料庫文檔**：ER 圖與資料表設計說明
- **部署指南**：從開發到生產的完整流程

### 測試與品質保證
- **E2E 測試**：PHPUnit E2E 測試
- **API 測試**：Postman 集合與環境配置
- **程式碼規範**：PSR 標準 + 自定義編碼規範

### CI/CD 流程
- **版本控制**：Git 工作流程與分支策略
- **部署自動化**：Docker 映像建構與推送
- **監控維護**：資源監控腳本與日誌管理

## 學習成果與技能展現

### 全端開發能力
- 從零開始設計並實作完整的 web 應用程式
- 掌握現代 PHP 框架（Laravel）與前端框架（Vue.js）
- 理解並實踐 RESTful API 設計原則

### 雲端部署經驗
- Docker 容器化應用程式部署
- GCP 雲端服務使用與資源管理
- 生產環境配置與效能調優

### 軟體架構設計
- 分層架構與設計模式應用
- 資料庫設計與效能優化
- 快取策略設計與實作

## 專案成果

- **成功上線**：穩定運行於生產環境
- **成本控制**：在免費雲端資源上實現完整功能
- **程式碼品質**：完整的文檔與 E2E 測試
- **可維護性**：清晰的架構與程式碼規範
- **擴展性**：模組化設計，易於功能擴展

## 專案連結

- **線上展示**：[aaronlei.com](https://aaronlei.com/)
- **程式碼**：[GitHub Repository](https://github.com/shianglin/aaronblog)
- **技術文檔**：專案內附完整技術文檔

---

*此專案展現了從需求分析、架構設計、程式開發到部署維護的完整軟體開發生命週期，特別著重於現代化技術應用與雲端資源優化。* 