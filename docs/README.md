# Aaron Blog 技術文檔

現代化全端個人部落格系統的完整技術文檔，採用 Laravel + Vue 技術棧，已成功部署於生產環境 ([aaronlei.com](https://aaronlei.com/))。

## 技術棧

- **後端**: Laravel 12 + PHP 8.2 + SQLite
- **前端**: Vue 3 + TypeScript + Naive UI  
- **快取**: Redis 7
- **部署**: Docker + GCP + GitHub Actions

## 文檔導覽

### 專案展示
- **[專案作品集](project-portfolio.md)** - 技術亮點與解決方案展示

### 系統架構
- **[架構總覽](architecture/README.md)** - 系統設計與技術選型
- **[認證系統](architecture/authentication.md)** - Session Cookie 認證架構
- **[資料庫設計](architecture/database.md)** - SQLite 資料庫設計
- **[快取策略](architecture/redis-strategy.md)** - Redis 快取與記憶體優化
- **[容器部署](architecture/docker-strategy.md)** - Docker 與 GCP 部署
- **[自動化流程](architecture/github-actions.md)** - CI/CD 流水線

### 技術決策
- **[架構決策記錄 (ADR)](adr/README.md)** - 重要技術決策的背景與演進

### 開發指南  
- **[測試指南](testing-guide.md)** - 測試策略與執行方法

### API 文檔
- **Swagger UI**: `/api/documentation` - 互動式 API 文檔
- **OpenAPI 規格**: `/api/documentation.json`

## 快速上手

### 常用指令
```bash
# 啟動開發服務器
php artisan serve

# 執行測試
php artisan test

# 查看路由
php artisan route:list

# 資料庫遷移
php artisan migrate
```

### 文檔使用指南

**新進開發者**
1. 閱讀 [架構總覽](architecture/README.md) 了解系統設計
2. 查看 [ADR 文檔](adr/README.md) 了解技術決策背景
3. 參考 [測試指南](testing-guide.md) 了解開發流程

**架構審查**
1. 檢視 [技術決策記錄](adr/README.md) 了解關鍵決策
2. 查閱 [系統架構](architecture/README.md) 了解技術選型
3. 參考 [專案作品集](project-portfolio.md) 了解技術成果

**運維部署**
1. 查看 [Docker 策略](architecture/docker-strategy.md) 了解容器化
2. 參考 [GitHub Actions](architecture/github-actions.md) 了解自動化流程

## 專案狀態

| 模組 | 狀態 | 說明 |
|-----|------|------|
| 認證系統 | 完成 | Laravel Sanctum Session Cookie |
| 文章管理 | 完成 | 完整 CRUD + 分類標籤 |
| 前端界面 | 完成 | Vue 3 + TypeScript |
| API 文檔 | 完成 | Swagger/OpenAPI 規範 |
| 自動化部署 | 完成 | GitHub Actions CI/CD |

**線上展示**: [aaronlei.com](https://aaronlei.com/)  
**最後更新**: 2025年 - 文檔架構優化與整合