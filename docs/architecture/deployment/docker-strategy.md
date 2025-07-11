# Docker 容器化策略

## 概述

Aaron Blog 採用 Docker 容器化部署，通過 Docker Compose 管理多個服務。設計重點在於開發環境與生產環境的一致性，以及針對雲端資源的優化。

## 容器架構

### 服務組成

```
┌─────────────────────────────────────┐
│            Docker Compose           │
├─────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐   │
│  │    nginx    │  │     app     │   │
│  │   (Web)     │  │  (Laravel)  │   │
│  │             │  │             │   │
│  └─────────────┘  └─────────────┘   │
│           │              │          │
│           └──────────────┘          │
│                  │                  │
│         ┌─────────────┐              │
│         │    redis    │              │
│         │   (Cache)   │              │
│         │             │              │
│         └─────────────┘              │
└─────────────────────────────────────┘
```

### 容器職責

- **nginx**: Web 伺服器，處理靜態檔案和反向代理
- **app**: Laravel 應用程式，PHP-FPM 執行環境
- **redis**: 快取服務，提供高效能資料快取

## 環境配置

### 開發環境 (docker-compose.yml)

**特色：**
- 完整的開發功能
- 即時程式碼同步
- 除錯模式開啟
- 非標準端口避免衝突

**配置重點：**
```yaml
services:
  app:
    build: .                    # 本地建構
    volumes:
      - .:/var/www             # 完整程式碼掛載
    environment:
      - APP_DEBUG=true         # 除錯模式
      - APP_ENV=local          # 本地環境
  
  nginx:
    ports:
      - "8080:80"              # 開發端口
  
  redis:
    ports:
      - "6380:6379"            # 開發端口
```

### 生產環境 (docker-compose.gcp.yml)

**特色：**
- 資源優化配置
- 記憶體限制設定
- 使用預建映像
- 標準端口配置

**配置重點：**
```yaml
services:
  app:
    image: aaronlei17/aaronblog-app:latest  # 預建映像
    mem_limit: 512m                        # 記憶體限制
    mem_reservation: 256m                  # 記憶體保留
    volumes:
      - ./public:/var/www/public:ro        # 唯讀掛載
      - sqlite-storage:/var/www/storage/app/database
  
  nginx:
    ports:
      - "80:80"                            # 標準端口
      - "443:443"                          # HTTPS 支援
    mem_limit: 128m                        # 記憶體限制
  
  redis:
    command: redis-server --maxmemory 100mb --maxmemory-policy allkeys-lru
    mem_limit: 128m                        # 記憶體限制
```

## Dockerfile 設計

### 多階段建構策略

```dockerfile
# 基於 PHP 8.2 FPM
FROM php:8.2-fpm

# 系統依賴安裝
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev \
    libsqlite3-dev sqlite3 \
    zip unzip git curl

# PHP 擴充安裝
RUN docker-php-ext-install pdo pdo_sqlite \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer 安裝
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# 應用程式設定
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader
```

### 最佳化策略

1. **層級快取優化**
   - 依賴安裝與程式碼複製分離
   - 最小化層級重建

2. **檔案權限管理**
   - 適當的 www-data 權限設定
   - 必要目錄的預建立

3. **生產環境優化**
   - `--no-dev` 排除開發依賴
   - `--optimize-autoloader` 效能優化

## 資源優化

### 記憶體管理

**GCP 免費層限制 (1GB RAM)**
- **app**: 512MB 限制 / 256MB 保留
- **nginx**: 128MB 限制 / 64MB 保留  
- **redis**: 128MB 限制 / 100MB 最大記憶體

### Redis 記憶體策略

```bash
# Redis 記憶體配置
redis-server --maxmemory 100mb --maxmemory-policy allkeys-lru
```

**策略說明：**
- `maxmemory 100mb`: 限制最大記憶體使用量
- `allkeys-lru`: 使用 LRU 演算法淘汰鍵值

### 磁碟空間優化

**SQLite 策略：**
- 生產環境使用 SQLite 檔案型資料庫
- 減少資料庫服務的記憶體佔用
- 簡化備份和遷移流程

## 網路配置

### 內部網路

```yaml
networks:
  aaronblog-net:
    driver: bridge
```

**服務通訊：**
- nginx ↔ app: 透過 PHP-FPM (9000 端口)
- app ↔ redis: 透過 Redis 協議 (6379 端口)

### 端口映射

| 環境 | 服務 | 內部端口 | 外部端口 |
|------|------|----------|----------|
| 開發 | nginx | 80 | 8080 |
| 開發 | redis | 6379 | 6380 |
| 生產 | nginx | 80 | 80 |
| 生產 | nginx | 443 | 443 |

## 資料持久化

### Volume 管理

**開發環境：**
```yaml
volumes:
  - .:/var/www                    # 完整程式碼同步
```

**生產環境：**
```yaml
volumes:
  - ./public:/var/www/public:ro   # 靜態檔案（唯讀）
  - sqlite-storage:/var/www/storage/app/database  # 資料庫檔案
  - .env:/var/www/.env            # 環境配置
```

### 資料備份策略

1. **SQLite 資料庫**
   - 定期備份 `storage/app/database` 目錄
   - 使用 Volume 確保資料持久化

2. **上傳檔案**
   - 掛載 `public` 目錄
   - 靜態檔案的持久化存儲

## 部署流程

### 建構流程

1. **本地建構**
   ```bash
   docker build -t aaronlei17/aaronblog-app:latest .
   ```

2. **推送映像**
   ```bash
   docker push aaronlei17/aaronblog-app:latest
   ```

3. **生產部署**
   ```bash
   docker-compose -f docker-compose.gcp.yml pull
   docker-compose -f docker-compose.gcp.yml up -d
   ```

### 更新策略

1. **滾動更新**
   - 先停止舊容器
   - 拉取新映像
   - 啟動新容器

2. **零停機部署**
   - 使用 nginx 的 graceful reload
   - 分階段容器重啟

## 監控與日誌

### 容器健康檢查

```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost/health"]
  interval: 30s
  timeout: 10s
  retries: 3
```

### 日誌管理

- **應用程式日誌**: Laravel 日誌系統
- **Web 伺服器日誌**: nginx 存取與錯誤日誌
- **容器日誌**: Docker 預設日誌驅動

## 安全配置

### 容器安全

1. **最小權限原則**
   - 使用 www-data 用戶
   - 避免 root 權限執行

2. **網路隔離**
   - 自定義網路隔離
   - 僅暴露必要端口

3. **映像安全**
   - 使用官方基礎映像
   - 定期更新依賴套件

---

*此 Docker 策略展現了從開發到生產的完整容器化方案，特別針對雲端資源限制進行了深度優化。* 