# Aaron Blog API Postman Collection

這個目錄包含了 Aaron Blog API 的 Postman Collection 檔案。你可以導入這些檔案到 Postman 中來測試 API。

## 檔案說明

- `aaron_blog_api.json` - 包含所有 API 請求的 Postman Collection
- `aaron_blog_api_environment.json` - 開發環境變數設定
- `aaron_blog_api_production_environment.json` - 生產環境變數設定

## API 分類

### 文章 API（公開）

- `GET /api/articles` - 獲取文章列表，支持分頁、排序、搜尋、標籤和分類篩選
- `GET /api/articles/{id}` - 獲取單篇文章

### 分類 API（公開）

- `GET /api/categories` - 獲取所有分類
- `GET /api/categories/{id}` - 獲取分類詳情

### 標籤 API（公開）

- `GET /api/tags` - 獲取所有標籤
- `GET /api/tags/{id}` - 獲取標籤詳情

### 認證 API

- `POST /api/auth/login` - 管理員登入
- `POST /api/auth/logout` - 管理員登出
- `GET /api/auth/user` - 獲取當前用戶資訊

### 管理員 API（需認證）

#### 文章管理
- `GET /api/admin/articles` - 獲取文章列表（管理員）
- `POST /api/admin/articles` - 創建文章
- `PUT /api/admin/articles/{id}` - 更新文章
- `DELETE /api/admin/articles/{id}` - 刪除文章

#### 分類管理
- `POST /api/admin/categories` - 創建分類
- `PUT /api/admin/categories/{id}` - 更新分類
- `DELETE /api/admin/categories/{id}` - 刪除分類

#### 標籤管理
- `POST /api/admin/tags` - 創建標籤
- `PUT /api/admin/tags/{id}` - 更新標籤
- `DELETE /api/admin/tags/{id}` - 刪除標籤

## 使用方法

1. 下載 [Postman](https://www.postman.com/downloads/) 並安裝
2. 在 Postman 中點擊 "File" -> "Import"
3. 選擇 `aaron_blog_api.json` 文件導入
4. 導入環境配置文件：
   - 開發環境：`aaron_blog_api_environment.json`
   - 生產環境：`aaron_blog_api_production_environment.json`
5. 在 Postman 右上角的環境下拉菜單中選擇環境
6. 使用 `/api/auth/login` 獲取 token
7. 開始測試 API

## 環境變數

### 開發環境

```json
{
  "baseUrl": "http://localhost:8000",
  "csrfToken": ""
}
```

### 生產環境

```json
{
  "baseUrl": "https://aaronlei.com",
  "csrfToken": ""
}
```

## 認證流程

1. 呼叫 `/sanctum/csrf-cookie` 獲取 CSRF Cookie
2. 呼叫 `/api/auth/login` API，傳入用戶名和密碼（需要 CSRF Token）
3. API 回應中會包含用戶資訊，Session Cookie 會自動設定
4. 之後所有需要認證的 API 請求會自動使用 Session Cookie，並需要提供 CSRF Token

## 注意事項

- 管理員相關 API 需要認證，必須先登入建立 Session
- 公開 API（文章、分類、標籤的查詢）無需認證
- 文章列表 API 的篩選參數都是選填的
- 生產環境的 API 有速率限制（每分鐘 30 次請求）
- 所有需要認證的 API 都需要提供 CSRF Token

## API 路徑說明

所有 API 請求都遵循以下路徑結構：

```
{{baseUrl}}/api/[資源]/[操作]
```

其中：
- `{{baseUrl}}` 是環境變數，對應開發環境的 `http://localhost:8000` 或生產環境的 `https://aaronlei.com`
- `/api` 是 API 的固定前綴
- `[資源]` 是 API 資源類型，如 `articles`、`categories`、`tags`
- `[操作]` 是對資源的操作，如 `list`、`show`

### 公開 API 路徑範例：
- 開發環境：`http://localhost:8000/api/articles`
- 生產環境：`https://aaronlei.com/api/articles`

### 管理員 API 路徑範例：
- 開發環境：`http://localhost:8000/api/admin/articles`
- 生產環境：`https://aaronlei.com/api/admin/articles`

## 文章標籤篩選格式

API 標準化使用陣列格式進行標籤篩選：

```
GET /api/articles?tags[]=laravel&tags[]=php
```

這種格式更清晰表達了多標籤的語義，並能正確處理包含特殊字元的標籤名稱。

## ID 與 Slug 使用策略

為了平衡系統效能與使用者體驗，我們採用以下策略：

1. **API 請求**：所有 API 使用 ID 作為資源識別符，確保最佳查詢效能
2. **前端路由**：建議前端使用 slug 呈現 URL，如 `/articles/how-to-build-laravel-app`
3. **映射機制**：前端應在內部維護 ID 與 slug 的映射關係

這種混合策略兼顧了系統效能與 SEO/使用者體驗。

## 速率限制

- **公開 API**：每分鐘 30 次請求
- **管理員 API**：每分鐘 30 次請求
- **認證 API**：無額外限制

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

## API 測試順序建議

1. **先測試公開 API**：測試文章、分類、標籤的查詢功能
2. **獲取 CSRF Token**：使用 `/sanctum/csrf-cookie` 取得 CSRF Cookie
3. **登入建立 Session**：使用 `/api/auth/login` 建立認證 Session
4. **測試管理員 API**：測試需要認證的 CRUD 操作
5. **測試認證相關**：測試 `/api/auth/user` 和 `/api/auth/logout`

這樣的順序可以確保測試的順利進行，並且能夠有效驗證 API 的完整功能。 