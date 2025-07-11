# 專案技術決策與挑戰分析

基於 Git 提交歷史的深度分析，記錄真實的技術演進過程與架構決策。

## 重要技術決策

### 1. 資料庫策略演進
- **早期決策**：混合使用 MySQL 與 SQLite
- **後期優化**：統一使用 SQLite 檔案型資料庫
- **檔案位置調整**：SQLite 檔案從預設位置移至 `storage/app/database`
- **技術考量**：簡化部署與維護成本，提升環境一致性

### 2. 軟刪除機制的取捨
- **初期實作**：為所有 Model 添加軟刪除功能
- **後期移除**：完全移除軟刪除機制 (2025_06_11)
- **技術考量**：簡化資料邏輯，減少查詢複雜度

### 3. 錯誤處理架構重構 (commit: 79ff569)
- **重構規模**：12個檔案，170行新增，133行刪除
- **原始設計**：Controller 層直接使用 ResponseMaker 處理錯誤
- **重構後**：Service 層拋出 Exception，Controller 專注流程控制
- **核心改進**：
  - 新增 BaseException 抽象基類，統一異常處理
  - 實作 ResourceNotFoundException 等具體異常
  - Controller 程式碼簡化（每個減少約10-18行）
  - 在 bootstrap/app.php 註冊全域異常處理器
- **技術價值**：清晰的職責分離，更好的可測試性

### 4. 快取策略設計 (commit: a3993b3)
- **重構規模**：9個檔案，266行新增，312行刪除
- **抽象設計**：BaseCacheService + CacheServiceInterface 
- **分層快取**：列表快取 (24小時) + 詳情快取 (3天)
- **Cache Tags**：利用 Redis 的 Tags 功能進行精確快取清除
- **複雜參數處理**：搜尋、排序、分頁參數的標準化與簽名生成
- **程式碼減少**：每個具體快取服務從~100行減少至~70行
- **介面統一**：cacheList、cacheDetail、clearResourceAllCache 等標準方法

### 5. 資源關聯限制處理 (commit: 9bb9621)
- **問題**：刪除分類/標籤時需要檢查文章關聯
- **解決方案**：
  - 新增 ResourceInUseException 自定義異常
  - 智慧錯誤訊息：包含資源類型、ID、使用數量
  - HTTP 409 狀態碼，符合 RESTful 標準
  - 在 Service 層實作業務邏輯檢查
- **技術細節**：「無法刪除分類（ID: 1），因為仍有 5 篇文章正在使用此分類」

## 架構優化歷程

### 1. Repository 模式實作 (commit: ddfa630)
- **重構規模**：8個檔案，152行新增，275行刪除
- **BaseRepository**：泛型設計 (@template T of Model) + Constructor Property Promotion
- **程式碼減少**：CategoryRepository 和 TagRepository 從~60行簡化為僅3行
- **技術現代化**：升級為 PHP 8+ 語法
- **保留優化**：ArticleRepository 保留精確查詢邏輯 (select 指定欄位 + with 預載入)
- **DRY 原則**：統一 CRUD 操作介面，消除重複程式碼

### 2. Service 層重構
- **Cache Service**：抽象基類 + 模板方法模式
- **快取失效策略**：精確的快取管理機制

### 3. API 回應標準化 (commit: 54fce30)
- **Transformer 模式**：實作 ArticleTransformer 進行資料轉換
- **ResponseMaker 增強**：新增 paginatorWithTransformer 方法
- **效能優化**：精確查詢和關聯載入策略
- **技術價值**：提高 API 響應的靈活性和維護性，同時最佳化資料庫查詢效能

### 4. 例外處理機制
- **BaseException**：統一的例外基類
- **錯誤代碼系統**：結構化的錯誤處理

## 前端架構優化

### 1. Vue 3 + TypeScript
- **Composition API**：現代化的組件開發
- **類型安全**：完整的 TypeScript 類型定義

### 2. 組件設計模式
- **可重用組件**：DataTable, FormModal, FilterBar
- **統一搜尋**：UnifiedSearchBar 組件設計

### 3. 架構重構 (commit: 4e5e872)
- **重構規模**：12個檔案，600行新增，61行刪除
- **程式碼分離**：類型定義與工具函數分離
- **新增結構**：
  - `/api` 目錄：HTTP 客戶端封裝
  - `/types` 目錄：TypeScript 類型定義
  - `/constants` 目錄：分頁等常數配置
  - `/utils` 目錄：日期處理等工具函數
- **依賴清理**：移除不必要的依賴

## 開發挑戰與解決方案

### 1. 前端編譯問題
- **挑戰**：Vite 編譯配置問題 (commit: c6a2027, e250c42)
- **解決**：調整前端編譯設定，支援 Markdown 渲染

### 2. Markdown 支援實作
- **挑戰**：從後端到前端的完整 Markdown 支援
- **解決**：前端 Markdown 編譯與渲染 (commit: e17e227)

### 3. 搜尋功能優化
- **迭代過程**：多次搜尋器重構 (commits: e8c3af7, f2d85b5)
- **技術改進**：精簡程式碼，提升使用者體驗

### 4. 測試架構調整
- **重構**：測試檔案結構重組 (commits: 63a88b7, a50911a)
- **分層**：Admin/Public/Auth 的清晰測試分類

## 測試策略

### 1. E2E 測試覆蓋
- **參數化測試**：DataProvider 模式
- **邊界條件**：空結果、超出範圍等測試
- **完整覆蓋**：分頁、搜尋、排序、過濾功能

### 2. 測試結構
- **分層測試**：Public API vs Admin API
- **清晰組織**：功能模組化的測試結構

## 部署與配置優化

### 1. 環境配置
- **開發環境**：SQLite + 簡化配置
- **生產環境**：SQLite + Docker + GCP + Cloudflare

### 2. 配置檔案管理
- **檔案重組**：配置檔案結構優化
- **環境分離**：開發與生產環境的配置分離

## 技術債務處理

### 1. 程式碼現代化
- **PHP 8+ 語法**：Constructor Property Promotion
- **強型別**：完整的類型註解

### 2. 文檔與規範
- **API 文檔**：Postman 集合與環境配置
- **程式碼規範**：PHPDoc 註解標準化

### 3. 效能優化
- **快取策略**：多層快取機制
- **查詢優化**：資料庫查詢最佳化

## 量化技術成果

### 程式碼品質提升
- **BaseRepository 重構**：減少 275 行重複程式碼 (123行淨減少)
- **快取服務重構**：重構後減少 46 行程式碼 (312→266)
- **錯誤處理重構**：重構12個檔案，淨增37行實現更好架構
- **前端架構優化**：新增 539 行結構化程式碼

### 架構設計能力
- **職責分離**：Controller、Service、Repository、Cache 各層職責明確
- **設計模式**：Repository、Transformer、Template Method、Factory 等模式應用
- **異常處理**：統一的異常基類 + 特定業務異常
- **泛型設計**：PHP 泛型註解提供型別安全

### 技術決策軌跡
1. **初期建立**：基礎 CRUD + 軟刪除機制
2. **架構重構**：Repository 模式 + 快取策略
3. **錯誤處理**：Exception 導向的錯誤處理架構  
4. **優化迭代**：移除軟刪除、簡化資料邏輯
5. **前端現代化**：TypeScript + 模組化架構

## 學習成果

這個專案展現了從基礎實作到架構優化的完整開發歷程：
- **迭代思維**：從可行解到最佳解的技術演進
- **重構能力**：大規模程式碼重構 (最大單次275行變更)
- **架構設計**：分層架構與設計模式的實際應用
- **效能優化**：快取策略與資料庫查詢最佳化
- **程式碼品質**：DRY原則、型別安全、錯誤處理 