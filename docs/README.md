# 部落格系統文件

本文件夾包含部落格系統的所有技術文件，包括系統架構、數據庫設計、API 文件等。

## 文件索引

### 系統文件

- [系統架構文件](ARCHITECTURE.md) - 系統架構和技術選型的詳細說明
- [需求規格文件](PRD.md) - 產品需求文件
- [資料庫設計](database.md) - 資料庫表結構設計
- [API 總覽](API.md) - API 一覽表及規範
- [Tailwind CSS 指南](tailwind-guide.md) - 前端樣式指南

### API 文件

API 文件詳細說明了系統所有 API 接口的用法。

- [API 總覽](api/README.md)

#### 認證相關

- [登入](api/auth/login.md) - 用戶登入 API
- [登出](api/auth/logout.md) - 用戶登出 API
- [用戶資訊](api/auth/user.md) - 獲取當前登入用戶資訊 API
- [認證流程詳解](api/auth/auth-flow.md) - Laravel 認證系統工作原理

#### 文章相關

- [文章管理 API](api/article/) - 文章相關 API

#### 通用

- [通用設定](api/common/) - 通用設定相關 API

### Postman 集合

- [API 測試集合](postman/) - Postman API 測試集合檔案 