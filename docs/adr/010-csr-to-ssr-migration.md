# ADR-010: CSR 到 SSR 的架構遷移

## Status
Accepted - 2025-09-26

## Context
**問題背景**: 現有系統採用 Vue.js SPA 客戶端渲染架構，面臨 SEO 困難、首屏載入時間長、搜尋引擎索引問題等挑戰，需要轉移至服務端渲染以提升使用者體驗和搜尋引擎可見度。

**相關 Commit**: 
- `c6dfbdb` - "refactor: 移除 Makefile 以簡化項目構建流程" (最終 CSR 版本)
- `4b05abf` - "refactor(frontend-ssr): migrate article detail to backend SSR and drop SPA implementation" (文章頁 SSR)
- `53750c7` - "feat(home): add SSR landing page" (首頁 SSR)

## Decision
**選擇的解決方案**: 逐步將 Vue.js SPA 架構遷移至 Laravel Blade SSR 架構，保持功能完整性的同時優化 SEO 和效能。

**選擇理由**:
- **SEO 優化需求**: 服務端渲染可讓搜尋引擎直接索引完整內容
- **效能提升**: 減少客戶端 JavaScript 載入時間，提升首屏渲染速度  
- **架構簡化**: 減少前後端複雜度，統一在 Laravel 生態系統內
- **既有投資保護**: 重用現有 Service 層和 Transformer 邏輯
- **漸進式遷移**: 分階段實施，降低風險

## Consequences

### ✅ 正面影響
- **SEO 大幅改善**: 搜尋引擎可直接讀取完整 HTML 內容
- **首屏效能提升**: 省去 JavaScript 解析和 API 請求時間
- **開發效率提升**: 單一技術棧，減少前後端協調成本
- **快取策略優化**: 可在服務端層面實施頁面快取策略
- **程式碼重用**: Service 層和 Transformer 邏輯完整保留

### ❌ 負面影響  
- **互動體驗略降**: 失去 SPA 的無刷新頁面切換體驗
- **伺服器負擔增加**: 每次請求都需要服務端渲染處理
- **開發模式轉變**: 需要適應 Blade 模板和服務端邏輯開發
- **功能限制**: 某些複雜前端互動需要額外 JavaScript 處理

### 📊 量化效果
- **首屏載入時間**: 預估減少 40-60%（省去 Vue.js bundle 載入）
- **SEO 分數提升**: Google Lighthouse SEO 評分預期從 70+ 提升至 90+
- **伺服器資源**: CPU 使用率預估增加 15-20%（渲染成本）
- **開發效率**: 單功能開發時間預估減少 25%（省去 API 設計協調）

## Implementation Details

### 遷移階段
1. **階段一** (v2.1.0 → v2.2.0): 文章詳情頁 SSR 轉換
   - 建立 ArticleViewController 和 article.blade.php
   - 導入俐落現代設計系統和 CSS 變數
   - 重用 ArticleService 和 ArticleTransformer
   
2. **階段二** (v2.2.0 → v2.3.0): 首頁 SSR 轉換
   - 實作 HomeController 搭配 home.blade.php
   - 完整搜尋、篩選、分頁功能移植
   - 動態版本資訊和社交媒體連結整合

### 技術架構
- **後端**: Laravel Blade 模板 + Controller
- **樣式**: CSS 模組化設計，重用 article.css 設計系統
- **JavaScript**: 漸進式增強，僅用於必要互動功能
- **資料層**: 完整保留 Service + Repository + Transformer 架構

## 相關決策
- [ADR-005](005-frontend-backend-separation.md) - 前後台邏輯完全分離
- [ADR-008](008-api-response-architecture.md) - API 回應與異常處理架構演進

---

*此決策記錄了從 Vue.js SPA 到 Laravel SSR 的完整架構遷移過程，標誌著系統在 SEO 優化和效能提升方面的重要里程碑。*