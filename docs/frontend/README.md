# 前端開發文件

本目錄包含與前端開發相關的文件，幫助開發者了解前端架構、設計和實現計劃。

## 文件索引

- [Tailwind CSS 指南](tailwind-guide.md) - Tailwind CSS 使用指南與最佳實踐 ✅
- [元件文件](components.md) - 前端元件說明 *(待完成)*
- [Vue.js 開發規範](vue-guidelines.md) - Vue.js 開發規範與最佳實踐 *(待完成)*

## 前端技術棧（計劃使用）

- **框架**：Vue.js 3 (Composition API)
- **路由**：Vue Router
- **狀態管理**：Pinia
- **CSS 框架**：Tailwind CSS
- **HTTP 客戶端**：Axios
- **構建工具**：Vite

## 項目結構（目前狀態）

```
resources/js/
├── api/              # API 請求封裝 ✅
│   └── article/      # 文章 API ✅
├── components/       # 可複用元件 ⏳
├── layouts/          # 頁面布局 ⏳
├── mock/             # 模擬資料 ✅
├── pages/            # 頁面元件 ⏳
├── router/           # 路由配置 ⏳
├── styles/           # 全局樣式 ⏳
└── types/            # TypeScript 類型定義 ⏳

✅ 已初步實作  |  ⏳ 計劃中
```

## 開發指南

### 安裝依賴

```bash
npm install
```

### 啟動開發服務器

```bash
npm run dev
```

### 構建生產版本

```bash
npm run build
```

## 前端開發計劃

1. **階段一：API 整合** ✅
   - 設計 API 服務層
   - 實作認證 API 整合
   - 實作文章 API 整合

2. **階段二：核心頁面** ⏳
   - 管理員登入頁面
   - 文章列表頁面
   - 文章編輯頁面

3. **階段三：UI 元件庫** ⏳
   - 表單元件
   - 導航元件
   - 通知元件

4. **階段四：前端優化** 🔮
   - 響應式設計完善
   - 效能優化
   - 動畫與互動

✅ 已完成  |  ⏳ 進行中  |  🔮 未來計劃

## 編碼規範（建議）

1. **元件命名**：使用 PascalCase（如 `ArticleCard.vue`）
2. **檔案命名**：使用 kebab-case（如 `article-card.vue`）
3. **使用 Composition API**：優先使用 `<script setup>` 語法
4. **類型安全**：盡可能使用 TypeScript 定義類型
5. **代碼註釋**：為複雜邏輯添加清晰的註釋

## 最佳實踐

1. **元件拆分**：將複雜頁面拆分為小型、可複用的元件
2. **邏輯抽離**：使用 Composables 抽離和復用業務邏輯
3. **響應式設計**：遵循移動優先的響應式設計方法
4. **性能優化**：
   - 使用 Lazy Loading 懶加載路由和元件
   - 避免不必要的計算和渲染
   - 合理使用 `v-memo` 和 `v-once` 