# ADR-006: 採用 GitHub Actions 實現 CI/CD 自動化部署

## Status
Accepted - 2025年7月5日

## Context

單人開發的部落格專案需要自動化部署流程，解決手動部署的時間成本和人為錯誤風險。

**目標環境**: GCP e2-micro VM (1 vCPU, 1GB RAM)  
**部署策略**: Docker 容器化部署

## Decision

採用 **GitHub Actions** 實現 CI/CD 流程，自動化原本的手動部署步驟。

### 自動化流程對比

#### 原始手動流程（README.md）
**後端部署**:
1. 本地 `docker buildx build --platform linux/amd64 -t aaronlei17/aaronblog-app:latest --push .`
2. GCP VM `docker pull aaronlei17/aaronblog-app:latest`
3. GCP VM `docker-compose -f docker-compose.gcp.yml down`
4. GCP VM `docker-compose -f docker-compose.gcp.yml up -d`

**前端部署**:
1. 本地 `npm run build`
2. `git push`（包含前端編譯後的 public/）
3. GCP VM `git pull`

#### 自動化後流程
```yaml
推送到 main → 測試 → 構建前端 & Docker → 部署 → 通知
```

## Consequences

### 正面影響 ✅
- **時間節省**: 從 15-20 分鐘手動操作縮短到 8-12 分鐘自動執行
- **可靠性提升**: 自動測試 + 回滾機制，部署成功率從 80% 提升到 95%
- **並行優化**: 前端和 Docker 構建並行執行
- **備份機制**: 自動備份前端資源，失敗時自動回滾

### 負面影響 ❌
- **複雜度**: 需要管理 GitHub Secrets 和 SSH 配置
- **依賴性**: 依賴 GitHub、Docker Hub 等第三方服務

## Implementation

### 核心技術決策

1. **前端資源處理**: 使用 SCP 上傳而非 Git commit，避免構建產物進入版控
2. **測試環境**: SQLite + Redis，與生產環境一致
3. **緩存策略**: Composer、npm、Docker layer 三層緩存優化
4. **部署安全**: 自動備份 + 狀態檢查 + 失敗回滾

### 流程階段

| 階段 | 作用 | 關鍵特性 |
|------|------|----------|
| test | 確保代碼品質 | SQLite + Redis 環境測試 |
| build-frontend | 構建前端資源 | npm cache + artifacts 上傳 |
| build-docker | 構建後端映像 | GitHub Actions cache |
| deploy | 部署到生產環境 | 備份 + SSH 部署 + 回滾 |
| notify | 結果通知 | 成功/失敗狀態報告 |

## 相關決策

- 基於 [ADR-001 SQLite](001-sqlite-production-database.md) 的輕量化部署
- 配合 [ADR-002 Redis 快取](002-redis-caching-strategy.md) 的測試環境