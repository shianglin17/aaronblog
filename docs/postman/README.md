# Aaron Blog API - Postman Collection

這個目錄包含 Aaron Blog API 的 Postman Collection，您可以使用它們快速測試和探索我們的 API。

## 檔案說明

- `aaron_blog_api.json` - 完整的 Postman Collection (尚未實現)
- `aaron_blog_api_environment.json` - Postman 環境變數設定 (尚未實現)

## 如何使用

1. 下載 [Postman](https://www.postman.com/downloads/) 或使用 Postman Web 版
2. 匯入 Collection 檔案：點擊 "Import" > 選擇 `aaron_blog_api.json`
3. 匯入環境變數：點擊 "Import" > 選擇 `aaron_blog_api_environment.json`
4. 在 Postman 右上角選擇環境 (例如 "Development", "Production")
5. 開始測試 API！

## 環境變數

使用環境變數可以更輕鬆地在不同環境之間切換，以下是主要的環境變數：

- `baseUrl` - API 的基礎 URL (例如 `http://localhost:8000/api` 或 `https://api.example.com/api`)
- `token` - 認證 Token，會在登入後自動設定

## 更新 Collection

當 API 有所變更時，我們會更新這些 Collection 檔案。請定期檢查並重新匯入最新的版本。 