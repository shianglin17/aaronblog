# 認證模組架構

## 模組概述

認證模組負責處理用戶的登入、登出和身份驗證功能。基於 Laravel Sanctum 的 Session Cookie 認證機制，為前後台分離架構提供安全且用戶友好的認證體驗。

> **技術決策背景**: 詳細的技術選型考量請參考 [ADR-003 Session Cookie + CSRF Token 認證機制](../../adr/003-session-csrf-authentication.md)

## 架構設計

### 核心組件層次

```
┌─────────────────────────────────────┐
│         認證模組架構                 │
├─────────────────────────────────────┤
│  HTTP Layer                         │
│  ├── AuthController (登入/登出)      │
│  ├── LoginRequest (請求驗證)         │
│  └── AdminOnly 中介軟體             │
├─────────────────────────────────────┤
│  Service Layer                      │
│  ├── Laravel Sanctum               │
│  ├── Session 管理                   │
│  └── CSRF 保護                      │
├─────────────────────────────────────┤
│  Storage Layer                      │
│  ├── sessions 表 (Session 資料)      │
│  └── users 表 (使用者資料)           │
└─────────────────────────────────────┘
```

### 認證流程設計

**登入流程**
```
前端請求 → CSRF Cookie → 登入 API → Session 建立 → 用戶資料回傳
```

**認證檢查流程**  
```
API 請求 → Session 驗證 → CSRF 檢查 → 權限驗證 → 業務邏輯
```

## API 端點設計

### 認證端點

| 端點 | 方法 | 描述 | 中介軟體 | 回應格式 |
|------|------|------|----------|----------|
| `/api/auth/login` | POST | 用戶登入 | web, guest | ApiResponse |
| `/api/auth/logout` | POST | 用戶登出 | web, auth:sanctum | ApiResponse |
| `/api/auth/user` | GET | 獲取用戶資訊 | web, auth:sanctum | ApiResponse |

### 權限控制設計

**路由分組策略**
- **公開 API**: `/api/articles`, `/api/categories` - 無需認證
- **管理 API**: `/api/admin/*` - 需要 `auth:sanctum` + `AdminOnly` 中介軟體

### 請求驗證架構

**LoginRequest 驗證層**
- Email 格式驗證與存在性檢查
- Password 必填驗證
- 中文錯誤訊息本地化
- 統一的 ApiResponse 錯誤格式

**認證失敗處理**
- ValidationException 自動轉換為 422 回應
- 提供具體的欄位錯誤訊息
- 避免敏感資訊洩漏

## 安全架構

### 核心安全機制

**Session 安全層**
- Session ID 重新生成防止 fixation 攻擊
- HttpOnly + SameSite=Lax cookie 配置
- Session 資料存儲在 SQLite 資料庫中

**CSRF 保護層**  
- 雙重 Token 驗證機制
- Laravel 自動 CSRF 中介軟體保護
- 前端 SPA 自動 token 管理

**權限控制層**
- AdminOnly 中介軟體控制後台存取
- 路由層級的認證要求
- API 端點權限分組

## 環境配置

### Sanctum 配置要點

**Stateful Domains**
```env
SANCTUM_STATEFUL_DOMAINS=localhost:8080,127.0.0.1:8080,aaronlei.com
```

**Session 配置**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax
SESSION_SECURE_COOKIE=true  # 生產環境
```

## 前端整合架構

### SPA 認證整合

**認證狀態管理**
- 透過 `/api/auth/user` 檢查登入狀態
- 401/419 錯誤自動處理機制
- CSRF token 自動同步更新

**錯誤處理策略**
- `401 未授權`: 重定向至登入頁面
- `419 CSRF 錯誤`: 自動重新獲取 token
- `422 驗證錯誤`: 顯示欄位級錯誤訊息

## 資料存儲架構

### Session 資料存儲

**存儲位置**: SQLite `sessions` 表
**關聯設計**: `sessions.user_id` → `users.id`
**清理策略**: 過期 session 自動清理機制

### 使用者資料管理

**認證相關欄位**
- `users.password`: Bcrypt 雜湊存儲
- `users.remember_token`: 「記住我」功能支援
- `users.email_verified_at`: 信箱驗證狀態

---

*認證模組基於 Laravel Sanctum 提供安全可靠的 Session Cookie 認證，詳細技術決策請參考相關 ADR 文件。* 