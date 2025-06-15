# Aaron Blog API 文檔

## 基本資訊

- **API 版本**：v1
- **基礎路徑**：`/api`
- **請求格式**：JSON
- **字符編碼**：UTF-8

## 核心概念

- [錯誤處理](error-handling.md) - HTTP 狀態碼、錯誤回應格式
- [認證機制](authentication.md) - Bearer Token 認證
- [分頁規範](pagination.md) - 分頁參數與回應格式
- [通用參數](common-parameters.md) - 排序、搜尋、日期格式
- [Rate Limiting](rate-limiting.md) - 請求頻率限制

## API 端點

### 公開 API

| 功能 | 端點 | 說明 |
|------|------|------|
| [文章列表](article/list.md) | `GET /api/articles` | 取得文章列表 |
| [文章詳情](article/show.md) | `GET /api/articles/{id}` | 取得單篇文章 |
| [分類列表](category/list.md) | `GET /api/categories` | 取得分類列表 |
| [分類詳情](category/show.md) | `GET /api/categories/{id}` | 取得單一分類 |
| [標籤列表](tag/list.md) | `GET /api/tags` | 取得標籤列表 |
| [標籤詳情](tag/show.md) | `GET /api/tags/{id}` | 取得單一標籤 |

### 認證 API

| 功能 | 端點 | 說明 |
|------|------|------|
| [登入](auth/login.md) | `POST /api/auth/login` | 用戶登入 |
| [登出](auth/logout.md) | `POST /api/auth/logout` | 用戶登出 |
| [用戶資訊](auth/user.md) | `GET /api/auth/user` | 取得當前用戶資訊 |

### 管理員 API

| 功能 | 端點 | 說明 |
|------|------|------|
| [文章管理](admin/articles.md) | `POST/PUT/DELETE /api/admin/articles` | 文章 CRUD 操作 |
| [分類管理](admin/categories.md) | `POST/PUT/DELETE /api/admin/categories` | 分類 CRUD 操作 |
| [標籤管理](admin/tags.md) | `POST/PUT/DELETE /api/admin/tags` | 標籤 CRUD 操作 | 