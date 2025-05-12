# Aaron Blog API - Postman Collection

這個目錄包含 Aaron Blog API 的 Postman Collection，您可以使用它們快速測試和探索我們的 API。

## 檔案說明

- `aaron_blog_api.json` - 完整的 Postman Collection
- `aaron_blog_api_environment.json` - 開發環境的環境變數設定
- `aaron_blog_api_production_environment.json` - 生產環境的環境變數設定

## 如何使用

1. 下載 [Postman](https://www.postman.com/downloads/) 或使用 Postman Web 版
2. 匯入 Collection 檔案：點擊 "Import" > 選擇 `aaron_blog_api.json`
3. 匯入環境變數：
   - 開發環境：點擊 "Import" > 選擇 `aaron_blog_api_environment.json`
   - 生產環境：點擊 "Import" > 選擇 `aaron_blog_api_production_environment.json`
4. 在 Postman 右上角選擇環境 (例如 "Aaron Blog API - 開發環境" 或 "Aaron Blog API - 生產環境")
5. 開始測試 API！

## API 路徑說明

所有 API 請求都遵循以下路徑結構：

```
{{baseUrl}}/api/[資源]/[操作]
```

其中：
- `{{baseUrl}}` 是環境變數，對應開發環境的 `http://localhost:8000` 或生產環境的 `https://api.aaronblog.com`
- `/api` 是 API 的固定前綴
- `[資源]` 是 API 資源類型，如 `article`
- `[操作]` 是對資源的操作，如 `list`

例如，獲取文章列表的完整 URL 是：
- 開發環境：`http://localhost:8000/api/article/list`
- 生產環境：`https://api.aaronblog.com/api/article/list`

## 環境變數

使用環境變數可以更輕鬆地在不同環境之間切換，以下是主要的環境變數：

- `baseUrl` - API 的基礎 URL (不包含 `/api` 前綴)
  - 開發環境: `http://localhost:8000`
  - 生產環境: `https://api.aaronblog.com`
- `token` - 認證 Token，會在登入後自動設定

## 包含的 API

目前這個 Collection 包含以下 API：

1. **文章相關**
   - 獲取文章列表 (`GET /api/article/list`) - 支援分頁、排序、搜尋、分類及標籤篩選功能
   - 獲取單篇文章 (`GET /api/article/:id`) - 使用文章 ID 獲取詳細內容

## 文章標籤篩選格式

API 標準化使用陣列格式進行標籤篩選：

```
GET /api/article/list?tags[]=laravel&tags[]=php
```

這種格式更清晰表達了多標籤的語義，並能正確處理包含特殊字元的標籤名稱。

## ID 與 Slug 使用策略

為了平衡系統效能與使用者體驗，我們採用以下策略：

1. **API 請求**：所有 API 使用 ID 作為資源識別符，確保最佳查詢效能
2. **前端路由**：建議前端使用 slug 呈現 URL，如 `/articles/how-to-build-laravel-app`
3. **映射機制**：前端應在內部維護 ID 與 slug 的映射關係

這種混合策略兼顧了系統效能與 SEO/使用者體驗。

## 更新 Collection

當 API 有所變更時，我們會更新這些 Collection 檔案。請定期檢查並重新匯入最新的版本。

## 手動匯出 Collection

如果您修改了 Collection 並想要更新儲存庫中的檔案，可以按照以下步驟操作：

1. 在 Postman 中選擇您的 Collection
2. 點擊右上角的三個點 (...)，然後選擇 "Export"
3. 選擇 "Collection v2.1" 格式
4. 下載 JSON 檔案並替換儲存庫中的 `aaron_blog_api.json`

同樣地，您也可以匯出環境變數設定：

1. 點擊右上角的齒輪圖示，然後選擇您的環境
2. 點擊編輯按鈕
3. 點擊右上角的三個點 (...)，然後選擇 "Export"
4. 下載 JSON 檔案並替換儲存庫中的對應環境檔案 