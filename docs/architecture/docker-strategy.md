# Docker 容器化架構

## 概述

Aaron Blog 採用三容器 Docker Compose 架構，專為 GCP e2-micro 免費層優化。通過環境差異化配置實現開發與生產的一致性，同時針對 1GB RAM 限制進行精細的記憶體管理。

## 服務架構設計

### 三層容器架構

```
┌─────────────────────────────────────┐
│        Docker Compose Stack        │
├─────────────────────────────────────┤
│  🌐 nginx (Alpine)                  │
│  ├── 靜態檔案服務 (/var/www/public) │
│  ├── PHP-FPM 代理 (app:9000)       │
│  └── 記憶體限制: 128MB              │
├─────────────────────────────────────┤
│  📱 app (PHP 8.2-FPM)              │
│  ├── Laravel 應用核心               │
│  ├── SQLite 資料庫存取              │
│  └── 記憶體限制: 512MB              │
├─────────────────────────────────────┤
│  💾 redis (7-alpine)               │
│  ├── 應用程式快取 (maxmemory 100MB) │
│  ├── Session 儲存                  │
│  └── 記憶體限制: 128MB              │
└─────────────────────────────────────┘
```

### 記憶體配置策略 (GCP e2-micro 1GB)

**記憶體分配精算**
- **app 容器**: 512MB limit / 256MB reserved
- **nginx 容器**: 128MB limit / 64MB reserved  
- **redis 容器**: 128MB limit + 100MB maxmemory
- **系統保留**: ~300MB (OS + Docker overhead)
- **總使用率**: ~95% 記憶體利用率

## 環境差異化配置

### 開發環境策略 (docker-compose.yml)

**開發最佳化特性**
- **即時開發**: 完整代碼目錄掛載 (`.:/var/www`)
- **除錯模式**: `APP_DEBUG=true` + `APP_ENV=local`
- **端口避衝**: nginx:8080, redis:6380 (非標準端口)
- **本地建構**: `build: .` 支援即時代碼修改
- **開發友好**: nginx 使用 `default.dev.conf` 配置

**Volume 掛載策略**
- **全目錄掛載**: 支援 Laravel artisan 指令和即時修改
- **無記憶體限制**: 開發環境不限制容器記憶體使用  
- **網路暴露**: Redis 6380 端口外露方便除錯工具連接

### 生產環境策略 (docker-compose.gcp.yml)

**GCP 部署最佳化**
- **預建映像**: `aaronlei17/aaronblog-app:latest` 減少部署時間
- **唯讀掛載**: `./public:/var/www/public:ro` 安全性最佳化
- **持久化存儲**: SQLite 透過 `sqlite-storage` volume 持久化
- **標準端口**: nginx 80/443 端口，符合生產環境需求

**記憶體精細控制**
- **app**: `mem_limit: 512m` + `mem_reservation: 256m`
- **nginx**: `mem_limit: 128m` + `mem_reservation: 64m`  
- **redis**: `mem_limit: 128m` + `--maxmemory 100mb --maxmemory-policy allkeys-lru`
- **重啟策略**: `restart: unless-stopped` 確保服務穩定性

## Dockerfile 架構設計

### 單階段 PHP-FPM 容器

**基礎映像**: `php:8.2-fpm` (官方 PHP-FPM 基礎映像)

**核心依賴安裝**
- **系統套件**: libpng-dev, libxml2-dev, libsqlite3-dev, sqlite3
- **PHP 擴充**: `pdo_mysql`, `pdo_sqlite`, `redis`  
- **工具程式**: git, curl, zip, unzip
- **Composer**: 從官方映像複製 `composer:2.5`

**目錄權限策略**
- **工作目錄**: `/var/www` (Laravel 標準配置)
- **權限設定**: `www-data:www-data` 擁有權
- **必要目錄**: 預建立 `storage/*`, `bootstrap/cache`, `database` 目錄

**生產環境優化**
- **Composer 安裝**: `--no-interaction --prefer-dist --optimize-autoloader --no-dev`
- **快取清理**: `rm -rf /var/lib/apt/lists/*` 減少映像大小  
- **檔案複製**: 透過 `.dockerignore` 排除不必要檔案

## 資料持久化策略

### Volume 管理架構

**開發環境 Volume**
- **完整掛載**: `.:/var/www` (即時開發支援)
- **靜態檔案**: `./public:/var/www/public` (nginx 直接存取)

**生產環境 Volume**
- **唯讀掛載**: `./public:/var/www/public:ro` (安全性優先)
- **SQLite 持久化**: `sqlite-storage:/var/www/storage/app/database`
- **日誌持久化**: `./storage/logs:/var/www/storage/logs`
- **環境配置**: `.env:/var/www/.env` (動態配置)

## 網路與端口策略

### Bridge 網路架構

**內部通訊網路**: `aaronblog-net` (Docker bridge)
- **nginx → app**: FastCGI 通訊 (app:9000)
- **app → redis**: Redis 協議 (redis:6379)  
- **內部 DNS**: 容器名稱自動解析

**端口映射策略**
- **開發環境**: nginx:8080, redis:6380 (避免本地衝突)
- **生產環境**: nginx:80/443 (標準 HTTP/HTTPS 端口)
- **內部端口**: redis:6379, app:9000 (不對外暴露)

---

*Docker 容器化架構為 Aaron Blog 提供環境一致性和 GCP 免費層優化，透過精細的記憶體管理實現最佳資源利用率。*
