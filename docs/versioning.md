# 版本管理規範

## 語義化版本控制

本專案採用語義化版本控制（Semantic Versioning），版本號格式為 `MAJOR.MINOR.PATCH`：

### 版本號規則

- **MAJOR（主版本號）**：當進行不相容的 API 修改時遞增
  - 例如：資料庫結構重大變更、API 介面破壞性修改
- **MINOR（次版本號）**：當新增功能且向下相容時遞增
  - 例如：新增 API 端點、新增功能模組
- **PATCH（修訂版本號）**：當進行向下相容的問題修正時遞增
  - 例如：Bug 修復、效能優化、安全性修補

## 版本發布

使用 `scripts/release.sh` 腳本進行版本發布：

- `./scripts/release.sh patch` - 發布修訂版本
- `./scripts/release.sh minor` - 發布次版本  
- `./scripts/release.sh major` - 發布主版本

## 版本同步

- Git 標籤與 Docker 映像檔標籤保持同步
- 每次發布都會同時更新 `latest` 標籤
- 建議在正式環境使用具體版本號而非 `latest` 