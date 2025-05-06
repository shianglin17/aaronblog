# Aaron Blog API 文件

這是 Aaron Blog 的 API 文件，提供了所有可用的 API 端點的詳細說明。

## 文件結構

- [通用規範](./common/README.md) - API 通用規範、認證方式、回應格式等
- 文章 API
  - [取得文章列表](./article/list.md) - 獲取文章列表，支援分頁和搜尋
- 認證 API
  - [登入](./auth/login.md) - 管理員登入系統，獲取 API Token
  - [登出](./auth/logout.md) - 管理員登出系統，廢除 API Token
  - [獲取用戶資訊](./auth/user.md) - 獲取當前登入用戶資訊
  - [認證流程詳解](./auth/auth-flow.md) - Laravel 認證系統工作原理詳解

## 開始使用

1. 閱讀 [通用規範](./common/README.md) 了解 API 的基本要求和格式
2. 了解 [認證流程詳解](./auth/auth-flow.md) 理解認證系統運作原理
3. 根據需求查閱具體的 API 文檔
4. 使用 Postman 或其他工具測試 API

## 認證方式

本 API 使用 Bearer Token 認證：

1. 先呼叫 `/api/auth/login` 獲取 Token
2. 在後續請求的 Header 中加入 `Authorization: Bearer {token}`
3. Token 過期或需要登出時，呼叫 `/api/auth/logout`

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