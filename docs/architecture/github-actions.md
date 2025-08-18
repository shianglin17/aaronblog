# GitHub Actions CI/CD 架構

## 概述

Aaron Blog 實現全自動化 CI/CD 流程，從程式碼提交到生產部署完全自動化。針對個人部落格特點設計，平衡了部署速度、安全性和資源使用效率。

> **相關決策**: 詳細的技術決策請參考 [ADR-006 GitHub Actions CI/CD](../../adr/006-github-actions-cicd.md)

## 流程架構設計

### 四階段流水線

```
┌─────────────────────────────────────┐
│          CI/CD Pipeline             │
├─────────────────────────────────────┤
│  📋 Test Stage                      │
│  ├── Laravel Feature Tests         │
│  ├── SQLite + Redis 環境           │
│  └── 程式碼品質檢查                  │
├─────────────────────────────────────┤
│  🎯 Build Stage (並行)              │
│  ├── Frontend Build (npm)          │
│  └── Docker Image Build            │
├─────────────────────────────────────┤
│  🚀 Deploy Stage                    │
│  ├── SSH 遠端部署                   │
│  ├── Frontend Assets 上傳          │
│  └── 服務重啟與健康檢查              │
├─────────────────────────────────────┤
│  📊 Monitor Stage                   │
│  └── 部署狀態通知                   │
└─────────────────────────────────────┘
```

### 觸發策略

**自動部署**: Push 到 main 分支
**手動部署**: workflow_dispatch (緊急發布)

## 階段架構詳解

### 1. 測試階段 (test)

**環境策略**
- **與生產一致**: SQLite + Redis 測試環境  
- **並行服務**: Redis 7-alpine 容器化測試
- **快取優化**: Composer 依賴快取機制

**測試涵蓋範圍**
- Laravel Feature 測試套件
- 資料庫 CRUD 操作驗證
- Redis 快取功能驗證  
- API 端點正確性測試

**品質門檻**: 所有測試必須通過才能進入建構階段

### 2. 建構階段 (並行執行)

**前端建構 (build-frontend)**
- **技術棧**: Node.js 18 + npm
- **建構工具**: Vite 前端建構系統
- **產物管理**: GitHub Actions Artifacts (1天保留)
- **快取策略**: npm 依賴快取

**Docker 建構 (build-docker)**  
- **建構平台**: linux/amd64 (GCP 相容)
- **映像策略**: 推送至 Docker Hub
- **快取優化**: GitHub Actions Cache (layer cache)
- **並行優勢**: 與前端建構同時進行，節省總時間

### 3. 部署階段 (deploy)

**部署策略**
- **目標環境**: GCP e2-micro VM  
- **部署方式**: SSH 遠端操作 + SCP 檔案傳輸
- **服務管理**: Docker Compose 容器編排
- **健康檢查**: 部署後自動服務狀態驗證

**部署流程**
1. **代碼同步**: Git pull 最新代碼
2. **映像更新**: Docker pull 最新映像  
3. **前端資產**: SCP 上傳建構後的前端檔案
4. **服務重啟**: Docker Compose 滾動重啟
5. **狀態檢查**: 服務健康檢查與日誌確認

## 安全與效能策略

### 安全配置

**Secrets 管理**
- `DOCKER_USERNAME` / `DOCKER_TOKEN`: Docker Hub 認證
- `GCP_HOST` / `GCP_USER`: SSH 連接資訊  
- `GCP_SSH_PRIVATE_KEY`: SSH 私鑰（僅 GitHub 存取）

**權限最小化原則**
- GitHub Actions 僅具備必要的 repository 權限
- GCP SSH 用戶僅具備部署相關權限
- Docker Hub token 限制推送權限

### 效能優化

**快取策略**
- **Composer 快取**: `~/.composer/cache` 基於 lock 檔案
- **npm 快取**: Node.js setup action 內建快取  
- **Docker 快取**: GitHub Actions Cache 儲存 layer cache

**並行執行**
- 前端建構與 Docker 建構並行執行
- 測試通過後立即啟動建構階段
- 減少總流水線執行時間約 40%

## 監控與維護

### 部署監控策略

**健康檢查機制**
- Docker Compose 服務狀態監控
- 應用程式 `/health` 端點檢查  
- 部署後自動服務驗證

**失敗處理**
- GitHub Actions 狀態通知
- 部署失敗自動報告 (commit SHA, 時間, 作者)
- 可擴展的通知渠道 (Email, Slack)

### 維護最佳實踐

**定期維護任務**
- GitHub Actions 版本更新
- 依賴套件安全更新
- SSH 密鑰定期輪換

**效能監控**
- 流水線執行時間追蹤
- 建構成功率統計
- 部署頻率分析

---

*GitHub Actions CI/CD 架構為 Aaron Blog 提供可靠的自動化部署，確保代碼品質與部署安全性。* 