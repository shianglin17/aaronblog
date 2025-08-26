# ADR-009: AWS EC2 部署環境遷移

## Status
Accepted - 2025-08-26

## Context
**問題背景**: GCP VM 成本考量下決定關閉，需要遷移至 AWS EC2 免費層繼續部署部落格應用，同時優化容器資源配置。

**相關 Commit**: `7bcdfaa` - "feat(aws): 新增 AWS EC2 部署配置"

## Decision
**選擇的解決方案**: 將部落格應用從 GCP 完全遷移至 AWS EC2 免費層。

**選擇理由**:
- 成本考量: GCP Compute Engine 持續運行超出免費層配額產生費用
- 網路費用: 使用 Cloudflare proxy 導致 GCP networking 產生額外計費
- 免費額度: AWS EC2 免費層提供更寬裕的運行時數配額 (750小時/月)
- 過渡策略: 保留 GCP 配置供過渡期使用，未來將完全移除

## Consequences

### ✅ 正面影響
- **性能提升**: 記憶體從 1GB 提升至 2GB，CPU 從 1 核提升至 2 核
- **容器資源**: App 容器記憶體從 512MB 提升至 1.2GB，Redis 從 128MB 提升至 512MB
- **備份簡化**: SQLite 改用伺服器綁定掛載，備份從複雜的 Docker Volume 操作簡化為 `cp -r` 命令
- **成本節約**: 關閉 GCP VM 避免持續的運行費用

### ❌ 負面影響
- **遷移期複雜**: 過渡期需要維護兩套部署配置
- **學習成本**: 需要熟悉 AWS EC2 的操作和監控方式
- **配置暫時分散**: Docker Hub 映像暫時需要支援兩套環境

### 📊 量化效果
- 記憶體提升: 100% (1GB → 2GB)
- CPU 提升: 100% (1 核 → 2 核)  
- App 容器記憶體: +135% (512MB → 1.2GB)  
- Redis 快取容量: +300% (100MB → 400MB)

## Implementation

### 核心技術決策

1. **數據庫策略**: 從 Docker Volume 改為直接綁定掛載
   ```yaml
   # 之前: sqlite-storage:/var/www/storage/app/database
   # 現在: ./storage/database:/var/www/storage/app/database
   ```

2. **分支隔離**: 使用獨立分支避免環境衝突
   - GCP: `main` 分支 → `docker-compose.gcp.yml`
   - AWS: `aws-production` 分支 → `docker-compose.aws.yml`

3. **權限簡化**: 移除複雜的 UID 設定，使用 Docker 預設權限
   - 理由: 優先確保部署穩定性，權限優化待後續處理

4. **網路配置**: 利用 Cloudflare 代理特性
   - 移除伺服器端 SSL 配置（443 端口）
   - AWS EC2 只需提供 HTTP (80)

### 資源配置對比

| 服務 | GCP 配置 | AWS 配置 | 提升幅度 |
|------|---------|---------|----------|
| App | 512MB/256MB | 1.2GB/600MB | +135% |
| Nginx | 128MB/64MB | 128MB/64MB | 持平 |
| Redis | 128MB, 100MB max | 512MB, 400MB max | +300% |

### 部署流程差異

| 階段 | GCP (deploy.yml) | AWS (deploy-aws.yml) | 主要差異 |
|------|------------------|---------------------|----------|
| 觸發分支 | main | aws-production | 環境隔離 |
| 映像標籤 | latest | latest | 統一管理 |
| 健康檢查 | 複雜回滾機制 | 簡化狀態檢查 | 減少複雜度 |

## 相關決策
- [ADR-006](006-github-actions-cicd.md) - GitHub Actions CI/CD 基礎
- [ADR-001](001-sqlite-production-database.md) - SQLite 生產資料庫策略