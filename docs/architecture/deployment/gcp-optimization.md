# GCP 資源優化策略

## 概述

Aaron Blog 專案針對 Google Cloud Platform (GCP) 免費層進行了深度優化，在有限的資源約束下實現了高效能的部落格系統。本文檔詳細說明了各項優化策略和實作細節。

## GCP 免費層限制

### 資源限制

**Compute Engine (e2-micro)**
- **CPU**: 1 vCPU（共享）
- **記憶體**: 1GB RAM
- **磁碟**: 30GB 標準持久磁碟
- **網路**: 1GB 出站流量/月
- **運行時間**: 每月 744 小時

**其他限制**
- **Cloud Storage**: 5GB
- **Cloud Functions**: 200 萬次調用/月
- **Cloud Run**: 200 萬次請求/月

### 成本考量

**免費層優勢**
- 無月費成本
- 適合小型專案
- 學習和測試用途

**潛在成本**
- 超出免費額度的使用
- 額外的網路流量
- 進階功能使用

## 記憶體優化策略

### 容器記憶體分配

**總記憶體預算：1GB**

```yaml
services:
  app:
    mem_limit: 512m        # 50% 記憶體分配
    mem_reservation: 256m  # 保證記憶體
    
  nginx:
    mem_limit: 128m        # 12.5% 記憶體分配
    mem_reservation: 64m   # 保證記憶體
    
  redis:
    mem_limit: 128m        # 12.5% 記憶體分配
    command: redis-server --maxmemory 100mb --maxmemory-policy allkeys-lru
```

**剩餘記憶體：~232MB**
- 系統保留：~200MB
- 緩衝區：~32MB

### Redis 記憶體優化

**配置策略**
```bash
# Redis 記憶體限制
--maxmemory 100mb

# LRU 淘汰策略
--maxmemory-policy allkeys-lru

# 持久化禁用（節省記憶體）
--save ""
--appendonly no
```

**優化效果**
- 記憶體使用量：100MB 上限
- 自動淘汰舊資料
- 避免記憶體溢出

### PHP 記憶體優化

**PHP-FPM 配置**
```ini
; 記憶體限制
memory_limit = 256M

; 進程管理
pm = dynamic
pm.max_children = 10
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
```

**Laravel 優化**
```php
// 配置快取
'cache' => [
    'default' => 'redis',
    'ttl' => 3600,
],

// 資料庫連接池
'database' => [
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => storage_path('app/database/database.sqlite'),
            'foreign_key_constraints' => true,
        ],
    ],
],
```

## 磁碟空間優化

### 資料庫策略

**SQLite vs MySQL**

| 特性 | SQLite | MySQL |
|------|--------|-------|
| 記憶體佔用 | 最小 | ~100MB |
| 磁碟空間 | 最小 | ~200MB |
| 維護成本 | 低 | 高 |
| 效能 | 讀取快 | 寫入快 |

**選擇 SQLite 的原因**
- 零配置需求
- 檔案型資料庫
- 適合小型應用
- 備份簡單

### 日誌管理

**日誌輪轉配置**
```yaml
logging:
  driver: json-file
  options:
    max-size: "10m"     # 單檔最大 10MB
    max-file: "3"       # 保留 3 個檔案
```

**清理腳本**
```bash
#!/bin/bash
# 清理舊日誌
find /var/log -name "*.log" -mtime +7 -delete

# 清理 Docker 日誌
docker system prune -f --volumes
```

### 檔案系統優化

**目錄結構**
```
/var/www/
├── storage/
│   ├── app/database/     # SQLite 資料庫
│   ├── logs/            # 應用程式日誌
│   └── cache/           # 檔案快取
├── public/              # 靜態檔案
└── bootstrap/cache/     # 框架快取
```

**掛載策略**
```yaml
volumes:
  # 唯讀掛載（節省記憶體）
  - ./public:/var/www/public:ro
  
  # 資料持久化
  - sqlite-storage:/var/www/storage/app/database
  
  # 配置檔案
  - .env:/var/www/.env
```

## 網路流量優化

### 內容優化

**Gzip 壓縮**
```nginx
# nginx 配置
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types
    text/plain
    text/css
    text/xml
    text/javascript
    application/javascript
    application/json;
```

**靜態資源優化**
```nginx
# 快取策略
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### CDN 整合

**Cloudflare 配置**
- 自動壓縮
- 圖片優化
- 快取加速
- SSL 終止

**流量節省**
- 靜態資源 CDN 快取
- 圖片自動優化
- 壓縮率提升 60-80%

## 效能監控

### 資源監控腳本

**Redis 記憶體監控**
```bash
#!/bin/bash
# scripts/redis-memory-monitor.sh

REDIS_CONTAINER="aaronblog-redis"
MAX_MEMORY_MB=100

# 獲取 Redis 記憶體使用量
MEMORY_USAGE=$(docker exec $REDIS_CONTAINER redis-cli INFO memory | grep used_memory_human | cut -d: -f2 | tr -d '\r')

echo "Redis 記憶體使用量: $MEMORY_USAGE"

# 檢查是否超過限制
MEMORY_MB=$(echo $MEMORY_USAGE | sed 's/M//' | sed 's/K//')
if [ "${MEMORY_MB%.*}" -gt $MAX_MEMORY_MB ]; then
    echo "警告: Redis 記憶體使用量超過限制"
    # 清理快取
    docker exec $REDIS_CONTAINER redis-cli FLUSHALL
fi
```

**系統資源監控**
```bash
#!/bin/bash
# 系統資源監控

echo "=== 系統資源使用情況 ==="
echo "記憶體使用:"
free -h

echo "磁碟使用:"
df -h

echo "容器資源:"
docker stats --no-stream
```

### 效能指標

**關鍵指標**
- 記憶體使用率 < 90%
- 磁碟使用率 < 80%
- 回應時間 < 500ms
- 可用性 > 99%

**監控頻率**
- 即時監控：每 5 分鐘
- 日誌分析：每小時
- 報告生成：每日

## 成本控制

### 免費額度管理

**監控項目**
- Compute Engine 使用時間
- 網路出站流量
- 磁碟使用量
- API 調用次數

**警告機制**
```bash
# 流量監控腳本
#!/bin/bash
TRAFFIC_LIMIT_GB=1
CURRENT_TRAFFIC=$(gcloud compute instances describe instance-name --format="value(networkInterfaces[0].accessConfigs[0].natIP)")

if [ $CURRENT_TRAFFIC -gt $TRAFFIC_LIMIT_GB ]; then
    echo "警告: 網路流量接近限制"
    # 發送通知
fi
```

### 資源調度

**自動縮放策略**
```yaml
# 基於負載的資源調整
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M
```

**負載均衡**
- 使用 nginx 負載均衡
- 基於 CPU 使用率調整
- 自動故障轉移

## 備份與恢復

### 備份策略

**自動備份**
```bash
#!/bin/bash
# 每日備份腳本
DATE=$(date +%Y%m%d)
BACKUP_DIR="/backup/$DATE"

# 建立備份目錄
mkdir -p $BACKUP_DIR

# 備份 SQLite 資料庫
cp storage/app/database/database.sqlite $BACKUP_DIR/

# 備份配置檔案
cp .env $BACKUP_DIR/

# 壓縮備份
tar -czf $BACKUP_DIR.tar.gz $BACKUP_DIR
rm -rf $BACKUP_DIR

# 清理舊備份（保留 7 天）
find /backup -name "*.tar.gz" -mtime +7 -delete
```

**雲端備份**
- Google Cloud Storage
- 自動同步
- 版本控制

### 災難恢復

**恢復流程**
1. 停止所有服務
2. 恢復資料庫檔案
3. 恢復配置檔案
4. 重新啟動服務
5. 驗證功能正常

**恢復腳本**
```bash
#!/bin/bash
# 災難恢復腳本
BACKUP_DATE=$1

if [ -z "$BACKUP_DATE" ]; then
    echo "使用方式: $0 YYYYMMDD"
    exit 1
fi

# 停止服務
docker-compose down

# 恢復檔案
tar -xzf /backup/$BACKUP_DATE.tar.gz -C /tmp/
cp /tmp/$BACKUP_DATE/database.sqlite storage/app/database/
cp /tmp/$BACKUP_DATE/.env .

# 重新啟動
docker-compose up -d

echo "恢復完成"
```

## 最佳實踐

### 部署最佳實踐

1. **資源監控**
   - 定期檢查資源使用情況
   - 設定警告閾值
   - 自動化監控腳本

2. **效能優化**
   - 定期清理暫存檔案
   - 優化資料庫查詢
   - 使用快取機制

3. **安全性**
   - 定期更新系統
   - 使用防火牆
   - 監控異常流量

### 故障排除

**常見問題**
1. **記憶體不足**
   - 重啟 Redis 服務
   - 清理快取
   - 調整記憶體分配

2. **磁碟空間不足**
   - 清理日誌檔案
   - 刪除舊備份
   - 壓縮資料庫

3. **網路流量超限**
   - 啟用更積極的快取
   - 優化圖片大小
   - 使用 CDN

**緊急處理**
```bash
# 緊急清理腳本
#!/bin/bash
echo "執行緊急清理..."

# 清理 Docker
docker system prune -f

# 清理日誌
truncate -s 0 /var/log/*.log

# 重啟服務
docker-compose restart

echo "清理完成"
```

---

*此 GCP 優化策略展現了在資源限制下實現高效能部署的完整方案，為小型專案提供了可行的雲端部署模式。* 