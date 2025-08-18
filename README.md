# AaronBlog

現代化全端個人部落格系統，採用 Laravel + Vue 技術棧，已成功部署於生產環境 [aaronlei.com](https://aaronlei.com/)。

後台參觀帳號密碼 (https://aaronblog.com/admin)
interviewer@aaronblog.com
interviewer

## 技術亮點

- **資源優化創新**：在 GCP 免費層 VM (1GB RAM) 上運行完整應用
- **分層架構設計**：Controller → Service → Repository → Model 清晰職責分工
- **現代化技術棧**：Laravel 12 + Vue 3 + TypeScript + SQLite + Redis
- **容器化部署**：Docker + GitHub Actions 自動化 CI/CD
- **效能優化**：多層快取策略，API 響應時間 < 100ms

## 技術棧

- **後端**: Laravel 12 + PHP 8.2 + SQLite
- **前端**: Vue 3 + Composition API + TypeScript + Naive UI
- **快取**: Redis 7 (記憶體優化配置)
- **認證**: Laravel Sanctum Session Cookie + CSRF
- **部署**: Docker + GCP + Cloudflare + GitHub Actions

## 核心功能

- **文章管理系統**：Markdown 編輯、分類標籤、狀態管理
- **前後台分離**：RESTful API 設計，公開 API vs 管理 API
- **快取策略**：分層快取 + Redis Tags 精確失效
- **安全機制**：Session Cookie 認證、CSRF 防護、輸入驗證
- **響應式設計**：支援桌面和行動裝置

## 快速開始

### 環境要求
- Docker & Docker Compose
- Node.js 18+
- Git

### 安裝步驟

```bash
# 1. 克隆專案
git clone https://github.com/shianglin/aaronblog.git
cd aaronblog

# 2. 設置環境配置
cp .env.example .env

# 3. 啟動 Docker 環境
docker-compose up -d

# 4. 進入應用容器初始化
docker exec -it aaronblog-app bash

# 在容器內執行初始化：
php artisan key:generate           # 生成應用密鑰
php artisan migrate                # 運行資料庫遷移  
php artisan db:seed                # 填充測試資料
exit

# 5. 安裝並構建前端資源
npm install
npm run build
```

### 訪問應用

- **前端界面**: http://localhost:8080
- **API 文檔**: http://localhost:8080/api/documentation  
- **管理後台**: http://localhost:8080/admin

### 開發模式

```bash
# 啟動開發服務器（支援熱重載）
npm run dev
```

## 專案展示

### 功能截圖
- 文章列表與詳情頁面
- 管理後台界面（文章、分類、標籤管理）
- 響應式設計展示
- Swagger API 文檔界面

### 技術特色
- **記憶體優化**：SQLite + Redis 100MB 限制，總記憶體使用 < 600MB
- **快取效能**：命中率 > 90%，首頁載入時間 < 2秒
- **程式碼品質**：完整測試覆蓋、PSR 規範、型別安全
- **自動化部署**：GitHub Actions 全自動 CI/CD 流水線

## 技術文檔

完整的技術文檔請查看 **[docs/](docs/)** 目錄：

- **[系統架構](docs/architecture/README.md)** - 技術選型與架構設計
- **[技術決策記錄](docs/adr/README.md)** - ADR 文檔與演進歷程  
- **[專案作品集](docs/project-portfolio.md)** - 技術亮點與解決方案
- **[測試指南](docs/testing-guide.md)** - 測試策略與開發流程

### API 文檔
本專案使用 Swagger (OpenAPI) 自動生成互動式 API 文件。

- **生產環境 API 文檔**: [https://aaronlei.com/docs](https://aaronlei.com/docs)
- **本地開發 API 文檔**: http://localhost:8080/docs
- **OpenAPI 規格 (JSON)**: http://aaronlei.com/api/documentation

## 專案成果

- **成功上線**：穩定運行於生產環境
- **成本控制**：在免費雲端資源上實現完整功能  
- **效能優化**：多層快取策略，API 響應速度提升 75%
- **程式碼品質**：重構減少 275 行重複代碼，提升維護效率
- **自動化流程**：完整的 CI/CD 流水線，支援自動測試與部署

## 開發環境

```bash
# 查看所有可用指令
php artisan list

# 執行測試
php artisan test

# 清除快取
php artisan cache:clear

# 查看路由列表  
php artisan route:list
```

## 聯絡方式

- **線上展示**: [aaronlei.com](https://aaronlei.com/)
- **GitHub**: [github.com/shianglin/aaronblog](https://github.com/shianglin/aaronblog)
- **技術部落格**: [aaronlei.com](https://aaronlei.com/)
