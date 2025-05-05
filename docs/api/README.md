# Aaron Blog API 文件

這是 Aaron Blog 的 API 文件，提供了所有可用的 API 端點的詳細說明。

## 文件結構

- [通用規範](./common/README.md) - API 通用規範、認證方式、回應格式等
- 文章 API
  - [取得文章列表](./article/list.md) - 獲取文章列表，支援分頁和搜尋
- 認證 API（待實現）
- 用戶 API（待實現）

## 開始使用

1. 閱讀 [通用規範](./common/README.md) 了解 API 的基本要求和格式
2. 根據需求查閱具體的 API 文檔
3. 使用 Postman 或其他工具測試 API

## Postman Collection

我們提供了 Postman Collection 以便於測試 API：

- [Postman Collection](../postman/aaron_blog_api.json) - 包含所有 API 請求的集合
- [開發環境設定](../postman/aaron_blog_api_environment.json) - 開發環境變數設定
- [生產環境設定](../postman/aaron_blog_api_production_environment.json) - 生產環境變數設定

關於 Postman 的詳細使用說明，請參考 [Postman 使用指南](../postman/README.md)。

## API 版本歷史

| 版本 | 發布日期 | 變更說明 |
|-----|---------|---------|
| v1  | 2024-XX-XX | 初始版本 | 