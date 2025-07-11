# 部署策略與環境配置

## 概述

Aaron Blog 採用多環境部署策略，支援本地開發、測試環境和生產環境。重點在於環境一致性、自動化部署和資源優化。

## 環境架構

### 環境分層

```
┌─────────────────────────────────────┐
│            Environment Tiers         │
├─────────────────────────────────────┤
│  Local Development                  │
│  ├── Docker Compose                │
│  ├── Hot Reload                    │
│  └── Debug Mode                    │
├─────────────────────────────────────┤
│  Production (GCP)                   │
│  ├── Docker Compose                │
│  ├── Resource Optimized            │
│  └── Performance Tuned             │
└─────────────────────────────────────┘
```

### 環境特性對比

| 特性 | 開發環境 | 生產環境 |
|------|----------|----------|
| 資料庫 | SQLite | SQLite |
| 快取 | Redis | Redis |
| 除錯模式 | 開啟 | 關閉 |
| 資源限制 | 無限制 | 嚴格限制 |
| 端口 | 8080 | 80/443 |
| 日誌等級 | DEBUG | ERROR |

## 部署環境

### 本地開發環境

**配置檔案：** `docker-compose.yml`

**特色：**
- 完整的開發工具鏈
- 即時程式碼同步
- 便於除錯和測試

**啟動流程：**
```bash
# 1. 環境準備
cp .env.example .env.development

# 2. 容器啟動
docker-compose up -d

# 3. 應用程式初始化
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 生產環境 (GCP)

**配置檔案：** `docker-compose.gcp.yml`

**特色：**
- 資源優化配置
- 高可用性設計
- 安全性強化

**部署流程：**
```bash
# 1. 環境準備
ssh aaron_gcp_e2_micro
cd /path/to/aaronblog

# 2. 程式碼更新
git pull origin main

# 3. 容器部署
docker-compose -f docker-compose.gcp.yml pull
docker-compose -f docker-compose.gcp.yml up -d

# 4. 健康檢查
docker-compose -f docker-compose.gcp.yml ps
```

## 自動化部署

### 發布腳本 (release.sh)

**功能特色：**
- 版本標籤管理
- Docker 映像建構
- 自動化推送
- 部署文檔生成

**使用方式：**
```bash
./scripts/release.sh v1.0.1 "修復文章搜尋功能的 bug"
```

**執行流程：**
1. **程式碼檢查**
   - 檢查未提交變更
   - 確認在 main 分支
   - 拉取最新程式碼

2. **版本管理**
   - 驗證版本號格式
   - 建立 Git 標籤
   - 推送到遠端倉庫

3. **映像建構**
   - 建構 Docker 映像
   - 標記版本和最新標籤
   - 推送到 Docker Hub

4. **配置更新**
   - 更新生產環境配置
   - 提交版本變更

### CI/CD 整合

**GitHub Actions 觸發：**
- 推送到 main 分支
- 建立 Release 標籤
- Pull Request 合併

**自動化流程：**
1. 程式碼品質檢查
2. 單元測試執行
3. Docker 映像建構
4. 部署到生產環境

## 環境配置管理

### 環境變數策略

**開發環境：**
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

**生產環境：**
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/storage/app/database/database.sqlite
CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

### 敏感資料管理

**安全原則：**
- 環境變數儲存敏感資料
- 不在版本控制中儲存機密
- 使用 `.env.example` 作為範本

**機密管理：**
- APP_KEY: Laravel 應用程式金鑰
- DB_PASSWORD: 資料庫密碼
- JWT_SECRET: JWT 簽名金鑰

## 資源優化策略

### GCP 免費層優化

**限制條件：**
- 1GB RAM 記憶體限制
- 30GB 磁碟空間
- 1GB 網路流量/月

**優化措施：**

1. **記憶體優化**
   ```yaml
   services:
     app:
       mem_limit: 512m
       mem_reservation: 256m
     nginx:
       mem_limit: 128m
       mem_reservation: 64m
     redis:
       mem_limit: 128m
       command: redis-server --maxmemory 100mb
   ```

2. **磁碟優化**
   - 使用 SQLite 檔案型資料庫
   - 壓縮日誌檔案
   - 定期清理暫存檔案

3. **網路優化**
   - 啟用 Gzip 壓縮
   - 使用 CDN 加速
   - 優化圖片大小

### 效能監控

**監控指標：**
- 記憶體使用率
- CPU 使用率
- 磁碟 I/O
- 網路流量

**監控腳本：**
```bash
# Redis 記憶體監控
./scripts/redis-memory-monitor.sh

# 系統資源監控
docker stats --no-stream
```

## 部署安全

### 容器安全

**安全措施：**
1. 使用非 root 用戶執行
2. 限制容器權限
3. 定期更新基礎映像
4. 掃描安全漏洞

**網路安全：**
```yaml
networks:
  aaronblog-net:
    driver: bridge
    driver_opts:
      com.docker.network.bridge.name: aaronblog-br
```

### 資料安全

**備份策略：**
1. **自動備份**
   - 每日備份 SQLite 資料庫
   - 每週備份完整專案

2. **備份驗證**
   - 定期恢復測試
   - 備份完整性檢查

**恢復流程：**
```bash
# 資料庫恢復
cp backup/database.sqlite storage/app/database/

# 檔案恢復
rsync -av backup/public/ public/
```

## 監控與日誌

### 應用程式監控

**日誌等級：**
- **開發環境**: DEBUG
- **生產環境**: ERROR

**日誌輪轉：**
```yaml
logging:
  driver: json-file
  options:
    max-size: "10m"
    max-file: "3"
```

### 健康檢查

**檢查項目：**
- Web 服務可用性
- 資料庫連接狀態
- Redis 快取狀態
- 磁碟空間使用

**檢查腳本：**
```bash
# 健康檢查
curl -f http://localhost/health || exit 1

# 服務狀態
docker-compose ps
```

## 故障排除

### 常見問題

1. **記憶體不足**
   ```bash
   # 檢查記憶體使用
   docker stats
   
   # 重啟服務
   docker-compose restart
   ```

2. **磁碟空間不足**
   ```bash
   # 清理 Docker 映像
   docker system prune -a
   
   # 清理日誌
   truncate -s 0 /var/log/*.log
   ```

3. **服務無法啟動**
   ```bash
   # 檢查日誌
   docker-compose logs -f
   
   # 重新建構
   docker-compose build --no-cache
   ```

### 緊急恢復

**快速恢復流程：**
1. 停止所有服務
2. 恢復最新備份
3. 重新啟動服務
4. 驗證功能正常

**恢復腳本：**
```bash
#!/bin/bash
# 緊急恢復腳本
docker-compose down
cp backup/database.sqlite storage/app/database/
docker-compose up -d
```

## 部署最佳實踐

### 部署檢查清單

**部署前檢查：**
- [ ] 程式碼測試通過
- [ ] 環境配置正確
- [ ] 資料庫遷移準備
- [ ] 備份已完成

**部署後驗證：**
- [ ] 服務正常啟動
- [ ] 健康檢查通過
- [ ] 功能測試完成
- [ ] 效能指標正常

### 版本管理

**版本號規則：**
- 格式：`vX.Y.Z`
- X: 主版本號（重大變更）
- Y: 次版本號（功能新增）
- Z: 修訂版本號（錯誤修復）

**發布策略：**
- 穩定版本：main 分支
- 開發版本：develop 分支
- 功能分支：feature/* 分支

---

*此部署策略確保了從開發到生產的順暢過渡，並針對雲端資源限制進行了深度優化。* 