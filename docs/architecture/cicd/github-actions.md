# GitHub Actions CI/CD 工作流程

## 概述

Aaron Blog 採用 GitHub Actions 實現完整的 CI/CD 流程，從程式碼提交到生產部署全程自動化。工作流程包括測試、建構、部署和監控等階段，確保程式碼品質和部署可靠性。

## 工作流程架構

### 流程圖

```
┌─────────────────────────────────────┐
│            GitHub Actions           │
├─────────────────────────────────────┤
│  Push to main branch                │
│           ↓                         │
│  ┌─────────────────┐                │
│  │   Run Tests     │                │
│  │   - PHPUnit     │                │
│  │   - Redis Test  │                │
│  │   - SQLite Test │                │
│  └─────────────────┘                │
│           ↓                         │
│  ┌─────────────────┐                │
│  │ Build Frontend  │                │
│  │   - npm ci      │                │
│  │   - npm build   │                │
│  │   - Upload      │                │
│  └─────────────────┘                │
│           ↓                         │
│  ┌─────────────────┐                │
│  │ Build Docker    │                │
│  │   - Buildx      │                │
│  │   - Push Hub    │                │
│  │   - Cache       │                │
│  └─────────────────┘                │
│           ↓                         │
│  ┌─────────────────┐                │
│  │ Deploy to GCP   │                │
│  │   - SSH Deploy  │                │
│  │   - SCP Assets  │                │
│  │   - Restart     │                │
│  └─────────────────┘                │
└─────────────────────────────────────┘
```

### 觸發條件

**自動觸發**
- Push 到 main 分支
- Pull Request 合併

**手動觸發**
- workflow_dispatch 事件
- 緊急部署需求

## 工作流程階段

### 1. 測試階段 (test)

**目標**: 確保程式碼品質和功能正確性

**環境配置**
```yaml
services:
  redis:
    image: redis:7-alpine
    ports:
      - 6379:6379
```

**執行步驟**
1. **代碼檢出**
   ```yaml
   - name: Checkout code
     uses: actions/checkout@v4
   ```

2. **PHP 環境設定**
   ```yaml
   - name: Setup PHP
     uses: shivammathur/setup-php@v2
     with:
       php-version: '8.2'
       extensions: mbstring, xml, ctype, json, curl, zip, pdo, sqlite, pdo_sqlite, redis
       tools: composer:v2
   ```

3. **依賴管理**
   ```yaml
   - name: Cache Composer dependencies
     uses: actions/cache@v3
     with:
       path: ~/.composer/cache
       key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
   ```

4. **測試執行**
   ```yaml
   - name: Execute tests
     run: php artisan test --testsuite=Feature
     env:
       DB_CONNECTION: sqlite
       DB_DATABASE: database/database.sqlite
       CACHE_STORE: redis
       REDIS_HOST: localhost
       REDIS_PORT: 6379
   ```

**測試範圍**
- Feature 測試
- 資料庫連接測試
- Redis 快取測試
- API 端點測試

### 2. 前端建構階段 (build-frontend)

**目標**: 編譯和優化前端資源

**依賴條件**
```yaml
needs: test
if: success()
```

**執行步驟**
1. **Node.js 環境設定**
   ```yaml
   - name: Setup Node.js
     uses: actions/setup-node@v4
     with:
       node-version: '18'
       cache: 'npm'
   ```

2. **依賴安裝**
   ```yaml
   - name: Install frontend dependencies
     run: npm ci
   ```

3. **資源建構**
   ```yaml
   - name: Build frontend assets
     run: npm run build
   ```

4. **產物上傳**
   ```yaml
   - name: Upload frontend artifacts
     uses: actions/upload-artifact@v4
     with:
       name: frontend-build
       path: public/build/
       retention-days: 1
   ```

**建構產物**
- 壓縮的 JavaScript 檔案
- 優化的 CSS 檔案
- 處理過的圖片資源
- 資源清單檔案

### 3. Docker 建構階段 (build-docker)

**目標**: 建構和推送 Docker 映像

**並行執行**
```yaml
needs: test
runs-on: ubuntu-latest
```

**執行步驟**
1. **Docker Buildx 設定**
   ```yaml
   - name: Set up Docker Buildx
     uses: docker/setup-buildx-action@v3
   ```

2. **Docker Hub 登入**
   ```yaml
   - name: Login to Docker Hub
     uses: docker/login-action@v3
     with:
       username: ${{ secrets.DOCKER_USERNAME }}
       password: ${{ secrets.DOCKER_TOKEN }}
   ```

3. **映像建構與推送**
   ```yaml
   - name: Build and push Docker image
     uses: docker/build-push-action@v5
     with:
       context: .
       platforms: linux/amd64
       push: true
       tags: aaronlei17/aaronblog-app:latest
       cache-from: type=gha
       cache-to: type=gha,mode=max
   ```

**優化策略**
- 使用 GitHub Actions 快取
- 多平台建構支援
- 層級快取優化
- 並行建構流程

### 4. 部署階段 (deploy)

**目標**: 部署到生產環境

**依賴條件**
```yaml
needs: [build-frontend, build-docker]
if: success()
```

**執行步驟**
1. **前端資源下載**
   ```yaml
   - name: Download frontend artifacts
     uses: actions/download-artifact@v4
     with:
       name: frontend-build
       path: public/build/
   ```

2. **SSH 部署**
   ```yaml
   - name: Deploy to GCP VM
     uses: appleboy/ssh-action@v1.0.3
     with:
       host: ${{ secrets.GCP_HOST }}
       username: ${{ secrets.GCP_USER }}
       key: ${{ secrets.GCP_SSH_PRIVATE_KEY }}
       script: |
         cd ~/aaronblog
         git pull origin main
         docker pull aaronlei17/aaronblog-app:latest
   ```

3. **前端資源上傳**
   ```yaml
   - name: Upload frontend assets
     uses: appleboy/scp-action@v0.1.7
     with:
       host: ${{ secrets.GCP_HOST }}
       username: ${{ secrets.GCP_USER }}
       key: ${{ secrets.GCP_SSH_PRIVATE_KEY }}
       source: "public/build/"
       target: "~/aaronblog/public/"
   ```

4. **服務重啟**
   ```yaml
   script: |
     cd ~/aaronblog
     docker-compose -f docker-compose.gcp.yml down
     docker-compose -f docker-compose.gcp.yml up -d
     sleep 10
     docker-compose -f docker-compose.gcp.yml ps
   ```

## 安全配置

### Secrets 管理

**必要的 Secrets**
```yaml
secrets:
  DOCKER_USERNAME: # Docker Hub 用戶名
  DOCKER_TOKEN: # Docker Hub 訪問令牌
  GCP_HOST: # GCP VM 的 IP 地址
  GCP_USER: # SSH 用戶名
  GCP_SSH_PRIVATE_KEY: # SSH 私鑰
```

**安全最佳實踐**
- 使用 GitHub Secrets 儲存敏感資料
- 定期輪換訪問令牌
- 限制 SSH 密鑰權限
- 使用最小權限原則

### 權限控制

**Repository 權限**
- Actions: read/write
- Contents: read
- Metadata: read
- Packages: write

**Docker Hub 權限**
- Repository: read/write
- 限制特定標籤推送

## 監控與通知

### 部署監控

**健康檢查**
```bash
# 服務狀態檢查
docker-compose -f docker-compose.gcp.yml ps

# 應用程式健康檢查
curl -f http://localhost/health
```

**監控指標**
- 容器運行狀態
- 服務回應時間
- 資源使用情況
- 錯誤日誌監控

### 失敗通知

**通知機制**
```yaml
- name: Send failure notification
  if: failure()
  run: |
    echo "❌ 部署失敗！"
    echo "時間: $(date)"
    echo "提交: ${{ github.sha }}"
    echo "分支: ${{ github.ref_name }}"
    echo "作者: ${{ github.actor }}"
```

**通知渠道**
- GitHub Actions 日誌
- Email 通知（可擴展）
- Slack 通知（可擴展）
- Discord 通知（可擴展）

## 效能優化

### 建構優化

**快取策略**
```yaml
# Composer 快取
- name: Cache Composer dependencies
  uses: actions/cache@v3
  with:
    path: ~/.composer/cache
    key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}

# NPM 快取
- name: Setup Node.js
  uses: actions/setup-node@v4
  with:
    node-version: '18'
    cache: 'npm'

# Docker 快取
cache-from: type=gha
cache-to: type=gha,mode=max
```

**並行執行**
- 前端建構與 Docker 建構並行
- 測試與建構分離
- 獨立的失敗處理

### 部署優化

**增量部署**
```bash
# 僅更新必要的容器
docker-compose -f docker-compose.gcp.yml pull
docker-compose -f docker-compose.gcp.yml up -d

# 漸進式重啟
docker-compose -f docker-compose.gcp.yml restart app
```

**回滾策略**
```bash
# 備份當前版本
cp -r public/build public/build.backup.$(date +%Y%m%d-%H%M%S)

# 清理舊備份
find ~/aaronblog/public -name "build.backup.*" -type d | sort -r | tail -n +4 | xargs rm -rf
```

## 故障排除

### 常見問題

1. **測試失敗**
   ```bash
   # 檢查測試日誌
   php artisan test --testsuite=Feature --verbose
   
   # 檢查 Redis 連接
   redis-cli ping
   ```

2. **Docker 建構失敗**
   ```bash
   # 檢查 Dockerfile
   docker build -t test-image .
   
   # 檢查快取
   docker system prune -a
   ```

3. **部署失敗**
   ```bash
   # 檢查 SSH 連接
   ssh -i ~/.ssh/private_key user@host
   
   # 檢查服務狀態
   docker-compose ps
   ```

### 除錯技巧

**本地測試**
```bash
# 模擬 CI 環境
docker run --rm -v $(pwd):/app -w /app php:8.2-cli composer install
docker run --rm -v $(pwd):/app -w /app php:8.2-cli php artisan test

# 測試 Docker 建構
docker build -t local-test .
docker run --rm -p 8080:80 local-test
```

**日誌分析**
```bash
# GitHub Actions 日誌
# 在 Actions 頁面查看詳細日誌

# 生產環境日誌
docker-compose -f docker-compose.gcp.yml logs -f
```

## 最佳實踐

### 工作流程設計

1. **階段分離**
   - 測試、建構、部署分離
   - 失敗快速反饋
   - 並行執行優化

2. **錯誤處理**
   - 每個階段的失敗處理
   - 詳細的錯誤日誌
   - 自動回滾機制

3. **安全性**
   - Secrets 管理
   - 權限最小化
   - 定期安全審查

### 維護策略

1. **定期更新**
   - GitHub Actions 版本更新
   - 依賴套件更新
   - 安全修補程式

2. **監控改進**
   - 效能指標追蹤
   - 部署成功率監控
   - 用戶體驗監控

3. **文檔維護**
   - 工作流程文檔更新
   - 故障排除指南
   - 最佳實踐分享

---

*此 GitHub Actions 工作流程展現了現代 CI/CD 的完整實踐，確保了程式碼品質和部署可靠性。* 