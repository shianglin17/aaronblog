# 發布流程管理

## 概述

Aaron Blog 採用規範化的發布流程，結合自動化腳本和手動驗證，確保每次發布的品質和可靠性。發布流程包括版本規劃、測試驗證、自動化部署和發布後監控。

## 版本管理策略

### 語義化版本控制

**版本號格式：`vX.Y.Z`**
- **X (主版本號)**: 重大變更，可能包含不相容的 API 變更
- **Y (次版本號)**: 功能新增，向後相容
- **Z (修訂版本號)**: 錯誤修復，向後相容

**版本範例**
```
v1.0.0 - 初始發布版本
v1.1.0 - 新增文章搜尋功能
v1.1.1 - 修復搜尋結果排序問題
v2.0.0 - 重構認證系統（破壞性變更）
```

### 分支策略

**主要分支**
```
main (生產分支)
├── develop (開發分支)
├── feature/* (功能分支)
├── hotfix/* (緊急修復分支)
└── release/* (發布分支)
```

**分支用途**
- **main**: 生產環境程式碼，每次合併觸發自動部署
- **develop**: 開發環境整合分支
- **feature/***: 功能開發分支
- **hotfix/***: 緊急修復分支
- **release/***: 發布準備分支

## 發布腳本 (release.sh)

### 腳本功能

**自動化流程**
```bash
./scripts/release.sh v1.2.0 "新增文章分類功能"
```

**執行步驟**
1. **環境檢查**
   - 檢查是否在 main 分支
   - 檢查是否有未提交的變更
   - 拉取最新程式碼

2. **版本驗證**
   - 驗證版本號格式
   - 檢查版本號是否已存在
   - 確認發布說明

3. **Git 標籤管理**
   - 建立帶註釋的 Git 標籤
   - 推送標籤到遠端倉庫
   - 更新版本紀錄

4. **Docker 映像建構**
   - 建構 Docker 映像
   - 標記版本和最新標籤
   - 推送到 Docker Hub

5. **部署配置更新**
   - 更新 docker-compose.gcp.yml
   - 提交配置變更
   - 推送到遠端倉庫

### 腳本實作細節

**版本驗證**
```bash
# 驗證版本號格式
if [[ ! $VERSION =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "❌ 版本號格式錯誤。請使用 vX.Y.Z 格式"
    exit 1
fi

# 檢查版本是否已存在
if git tag -l | grep -q "^$VERSION$"; then
    echo "❌ 版本 $VERSION 已存在"
    exit 1
fi
```

**Docker 映像管理**
```bash
# 建構映像
echo "🏗️  建構 Docker 映像..."
docker build -t aaronlei17/aaronblog-app:$VERSION .
docker build -t aaronlei17/aaronblog-app:latest .

# 推送映像
echo "📦 推送 Docker 映像到 Docker Hub..."
docker push aaronlei17/aaronblog-app:$VERSION
docker push aaronlei17/aaronblog-app:latest
```

**配置更新**
```bash
# 更新生產環境配置
sed -i "s|aaronlei17/aaronblog-app:.*|aaronlei17/aaronblog-app:$VERSION|g" docker-compose.gcp.yml

# 提交變更
git add docker-compose.gcp.yml
git commit -m "chore: 更新生產環境映像版本至 $VERSION"
git push origin main
```

## 發布流程

### 1. 發布前準備

**程式碼審查**
- [ ] 功能完整性測試
- [ ] 程式碼品質檢查
- [ ] 安全性審查
- [ ] 效能測試

**環境準備**
```bash
# 確保在 main 分支
git checkout main
git pull origin main

# 檢查工作目錄乾淨
git status

# 執行完整測試
php artisan test
npm run test
```

**版本規劃**
- 確定版本號
- 撰寫發布說明
- 準備變更日誌
- 通知相關人員

### 2. 執行發布

**自動化發布**
```bash
# 執行發布腳本
./scripts/release.sh v1.2.0 "新增文章分類功能和標籤管理"
```

**手動驗證**
```bash
# 檢查標籤建立
git tag -l | grep v1.2.0

# 檢查 Docker 映像
docker images | grep aaronlei17/aaronblog-app

# 檢查 Docker Hub
curl -s https://hub.docker.com/v2/repositories/aaronlei17/aaronblog-app/tags/ | jq '.results[].name'
```

### 3. 部署驗證

**自動部署觸發**
- GitHub Actions 自動偵測 main 分支推送
- 執行完整 CI/CD 流程
- 自動部署到生產環境

**部署監控**
```bash
# 監控部署狀態
watch -n 5 "curl -s https://aaronlei.com/health | jq '.status'"

# 檢查容器狀態
ssh gcp-vm "cd ~/aaronblog && docker-compose -f docker-compose.gcp.yml ps"
```

### 4. 發布後驗證

**功能測試**
- [ ] 首頁載入正常
- [ ] 文章列表顯示正確
- [ ] 文章詳情頁面正常
- [ ] 搜尋功能正常
- [ ] 管理後台正常

**效能監控**
```bash
# 回應時間測試
curl -w "@curl-format.txt" -o /dev/null -s https://aaronlei.com/

# 資源使用監控
ssh gcp-vm "docker stats --no-stream"
```

**錯誤監控**
```bash
# 檢查應用程式日誌
ssh gcp-vm "cd ~/aaronblog && docker-compose -f docker-compose.gcp.yml logs -f app"

# 檢查 Nginx 日誌
ssh gcp-vm "cd ~/aaronblog && docker-compose -f docker-compose.gcp.yml logs -f nginx"
```

## 回滾策略

### 快速回滾

**自動回滾**
```bash
# 回滾到上一個版本
git log --oneline -n 5
git checkout <previous-commit-hash>

# 重新部署
./scripts/release.sh v1.1.9 "緊急回滾至穩定版本"
```

**手動回滾**
```bash
# SSH 到生產環境
ssh gcp-vm

# 切換到備份版本
cd ~/aaronblog
docker-compose -f docker-compose.gcp.yml down
docker run -d --name temp-backup aaronlei17/aaronblog-app:v1.1.8
docker-compose -f docker-compose.gcp.yml up -d
```

### 資料庫回滾

**備份恢復**
```bash
# 停止服務
docker-compose -f docker-compose.gcp.yml down

# 恢復資料庫
cp backup/database-$(date +%Y%m%d).sqlite storage/app/database/database.sqlite

# 重新啟動
docker-compose -f docker-compose.gcp.yml up -d
```

## 發布通知

### 自動通知

**GitHub Release**
```bash
# 建立 GitHub Release
gh release create v1.2.0 \
  --title "v1.2.0 - 新增文章分類功能" \
  --notes "## 新功能\n- 文章分類管理\n- 標籤系統\n\n## 修復\n- 修復搜尋排序問題" \
  --prerelease=false
```

**部署通知**
```bash
# 發送部署通知
curl -X POST "https://hooks.slack.com/services/xxx" \
  -H "Content-Type: application/json" \
  -d "{\"text\":\"🚀 Aaron Blog v1.2.0 已成功部署到生產環境\"}"
```

### 變更日誌

**CHANGELOG.md 更新**
```markdown
## [1.2.0] - 2024-01-15

### 新增
- 文章分類管理功能
- 標籤系統整合
- 分類頁面導航

### 修復
- 修復搜尋結果排序問題
- 改善文章載入效能

### 變更
- 更新 UI 設計風格
- 優化手機端體驗
```

## 發布檢查清單

### 發布前檢查

**程式碼品質**
- [ ] 所有測試通過
- [ ] 程式碼審查完成
- [ ] 無 linting 錯誤
- [ ] 安全性掃描通過

**功能驗證**
- [ ] 新功能測試完成
- [ ] 回歸測試通過
- [ ] 效能測試滿足要求
- [ ] 相容性測試通過

**部署準備**
- [ ] 環境配置正確
- [ ] 資料庫遷移準備
- [ ] 備份計劃確認
- [ ] 回滾計劃準備

### 發布後檢查

**部署驗證**
- [ ] 服務正常啟動
- [ ] 健康檢查通過
- [ ] 關鍵功能正常
- [ ] 效能指標正常

**監控確認**
- [ ] 錯誤率正常
- [ ] 回應時間正常
- [ ] 資源使用正常
- [ ] 用戶體驗正常

**文檔更新**
- [ ] 變更日誌更新
- [ ] API 文檔更新
- [ ] 使用者手冊更新
- [ ] 技術文檔更新

## 緊急發布流程

### 熱修復流程

**快速修復**
```bash
# 建立熱修復分支
git checkout -b hotfix/v1.2.1 main

# 修復問題
# ... 進行修復 ...

# 測試修復
php artisan test

# 合併到 main
git checkout main
git merge hotfix/v1.2.1

# 緊急發布
./scripts/release.sh v1.2.1 "緊急修復：修復登入驗證問題"
```

**緊急部署**
```bash
# 跳過完整測試（僅限緊急情況）
git push origin main

# 監控部署狀態
watch -n 10 "curl -s https://aaronlei.com/health"
```

## 最佳實踐

### 發布節奏

**定期發布**
- 每週一次小版本發布
- 每月一次功能版本發布
- 必要時進行緊急修復

**發布時機**
- 避免週五發布
- 避免節假日發布
- 選擇使用者活動較少的時段

### 風險管理

**風險評估**
- 評估變更影響範圍
- 識別潛在風險點
- 準備應急預案

**監控策略**
- 設定關鍵指標警報
- 建立監控儀表板
- 定期檢查系統健康

### 團隊協作

**溝通機制**
- 發布前通知相關人員
- 發布狀態即時更新
- 發布後總結分享

**知識分享**
- 記錄發布經驗
- 分享最佳實踐
- 持續改進流程

---

*此發布流程確保了 Aaron Blog 的每次發布都是可控、可靠且可追蹤的，為持續交付奠定了堅實基礎。* 