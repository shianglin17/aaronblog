# Aaron Blog API Postman Collection

這個目錄包含了 Aaron Blog API 的 Postman Collection 檔案。你可以導入這些檔案到 Postman 中來測試 API。

## 檔案說明

- `aaron_blog_api.json` - 包含所有 API 請求的 Postman Collection
- `aaron_blog_api_environment.json` - 開發環境變數設定
- `aaron_blog_api_production_environment.json` - 生產環境變數設定

## API 分類

### 文章 API

- `GET /api/article/list` - 獲取文章列表，支持分頁、排序、搜尋、標籤和分類篩選
- `GET /api/article/{id}` - 獲取單篇文章
- `POST /api/article` - 創建文章（需管理員權限）
- `PUT /api/article/{id}` - 更新文章（需管理員權限）
- `DELETE /api/article/{id}` - 刪除文章（需管理員權限）
- `PATCH /api/article/{id}/publish` - 發布文章（需管理員權限）
- `PATCH /api/article/{id}/draft` - 將文章設為草稿（需管理員權限）

### 分類 API
- `GET /api/categories` - 獲取所有分類

### 標籤 API
- `GET /api/tags` - 獲取所有標籤
- `GET /api/tags/{id}` - 獲取標籤詳情
- `POST /api/admin/tags` - 創建標籤（需管理員權限）
- `PUT /api/admin/tags/{id}` - 更新標籤（需管理員權限）
- `DELETE /api/admin/tags/{id}` - 刪除標籤（需管理員權限）

### 認證 API

- `POST /api/auth/login` - 管理員登入
- `POST /api/auth/logout` - 管理員登出
- `GET /api/auth/user` - 獲取當前用戶資訊

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
  "token": ""
}
```

### 生產環境

```json
{
  "baseUrl": "https://api.aaronblog.com",
  "token": ""
}
```

## 認證流程

1. 呼叫 `/api/auth/login` API，傳入用戶名和密碼
2. API 回應中會包含 token
3. 將 token 複製到環境變數中的 `token` 欄位
4. 之後所有需要認證的 API 請求會自動加上 Authorization header

## 注意事項

- 某些 API 需要管理員權限，必須先登入取得 token
- 文章列表 API 的篩選參數都是選填的
- 生產環境的 API 可能會有速率限制

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