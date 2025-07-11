# 認證模組架構

## 模組概述

認證模組負責處理用戶的登入、登出和身份驗證功能。本系統採用 Laravel Sanctum 的 Session Cookie 認證機制，提供安全且用戶友好的認證體驗。

## 技術選型

### 認證機制演進
- **初期**：Laravel Sanctum API Token 認證
- **現行**：Laravel Sanctum Session Cookie 認證
- **選擇原因**：
  - 更好的用戶體驗（無需手動管理 Token）
  - 自動處理 CSRF 保護
  - 與傳統 Web 應用程式整合更佳
  - 支援前後端分離架構

## 架構設計

### 核心組件

```
┌─────────────────────────────────────┐
│           Authentication Module      │
├─────────────────────────────────────┤
│  AuthController                     │
│  ├── login()                        │
│  ├── logout()                       │
│  ├── user()                         │
│  └── register()                     │
├─────────────────────────────────────┤
│  LoginRequest                       │
│  ├── 驗證規則                        │
│  └── 錯誤訊息                        │
├─────────────────────────────────────┤
│  Laravel Sanctum                   │
│  ├── Session Cookie 管理            │
│  ├── CSRF 保護                      │
│  └── 認證狀態管理                    │
└─────────────────────────────────────┘
```

### 認證流程

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   前端      │     │AuthController│     │   Session   │
│   登入請求   │────▶│   login()    │────▶│   建立      │
└─────────────┘     └─────────────┘     └─────┬───────┘
                                              │
                                              ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   前端      │     │   Response   │     │   用戶資料   │
│   獲得認證   │◀────│ (user data)  │◀────│   回傳      │
└─────────────┘     └─────────────┘     └─────────────┘
```

## 實作細節

### AuthController 核心方法

#### 1. 登入方法 (`login()`)

**實現流程：**
1. 驗證登入資料（email 和 password）
2. 使用 `web` guard 嘗試認證用戶
3. 若認證失敗，回傳錯誤訊息
4. 若認證成功，重新產生 session ID 防止 session fixation 攻擊
5. 回傳用戶資料

**程式碼結構：**
```php
public function login(LoginRequest $request): JsonResponse
{
    $credentials = $request->validated();
    
    if (!Auth::guard('web')->attempt($credentials, true)) {
        throw ValidationException::withMessages([
            'email' => ['提供的憑證不正確。'],
        ]);
    }
    
    $request->session()->regenerate();
    $user = Auth::guard('web')->user();
    
    return ResponseMaker::success([
        'user' => $user
    ], message: '登入成功');
}
```

#### 2. 登出方法 (`logout()`)

**實現流程：**
1. 登出當前用戶的 session
2. 清除 session 資料
3. 重新產生 CSRF token

#### 3. 用戶資訊方法 (`user()`)

**功能：**
- 獲取當前認證用戶資訊
- 用於前端檢查認證狀態

### 請求驗證 (LoginRequest)

**驗證規則：**
- `email`: 必填、有效的電子郵件格式
- `password`: 必填、字串格式

**自定義錯誤訊息：**
- 提供中文錯誤訊息
- 統一的驗證失敗回應格式

## 安全策略

### 1. Session 安全
- **Session Regeneration**：登入成功後重新產生 session ID
- **Session Invalidation**：登出時清除所有 session 資料
- **Session Timeout**：配置適當的 session 生命週期

### 2. CSRF 保護
- 自動啟用 CSRF 保護
- 前端自動處理 CSRF token
- 錯誤時自動重新獲取 token

### 3. 認證狀態管理
- 使用 Laravel 的 `web` guard
- 支援「記住我」功能
- 自動處理認證狀態檢查

## API 端點

### 認證相關端點

| 端點 | 方法 | 描述 | 認證需求 |
|------|------|------|----------|
| `/api/auth/login` | POST | 用戶登入 | 無 |
| `/api/auth/logout` | POST | 用戶登出 | 需要認證 |
| `/api/auth/user` | GET | 獲取用戶資訊 | 需要認證 |

### 回應格式

**成功登入回應：**
```json
{
  "status": "success",
  "code": 200,
  "message": "登入成功",
  "data": {
    "user": {
      "id": 1,
      "name": "Aaron",
      "email": "admin@example.com",
      "created_at": "2024-05-10T10:00:00.000000Z",
      "updated_at": "2024-05-10T10:00:00.000000Z"
    }
  }
}
```

**認證失敗回應：**
```json
{
  "status": "error",
  "code": 401,
  "message": "提供的憑證不正確。",
  "data": null
}
```

## 前端整合

### 認證狀態檢查
- 前端可透過 `/api/auth/user` 端點檢查認證狀態
- 使用 `X-Skip-Auth-Redirect` 標頭避免自動重定向

### CSRF Token 管理
- 前端自動從 meta tag 或 cookie 獲取 CSRF token
- 認證錯誤時自動重新獲取 token

### 錯誤處理
- 401 未授權：自動重定向到登入頁面
- 419 CSRF 錯誤：自動重試請求

## 配置設定

### Sanctum 配置
```php
// config/sanctum.php
'stateful' => [
    'localhost',
    'localhost:3000',
    'localhost:8080',
    '127.0.0.1',
    '127.0.0.1:8000',
    'aaronlei.com'
],
'guard' => ['web'],
'expiration' => null,
```

### Session 配置
- 使用 Redis 作為 session 驅動
- 配置適當的 session 生命週期
- 啟用 secure cookie（生產環境）

## 測試策略

### 認證測試涵蓋範圍
1. **登入功能測試**
   - 有效憑證登入
   - 無效憑證登入
   - 格式驗證

2. **登出功能測試**
   - 成功登出
   - Session 清除驗證

3. **認證狀態測試**
   - 認證用戶資訊獲取
   - 未認證用戶處理

4. **安全測試**
   - CSRF 保護測試
   - Session 安全測試

## 最佳實踐

### 1. 安全考量
- 使用 HTTPS 傳輸認證資料
- 實施適當的 rate limiting
- 記錄認證相關的安全事件

### 2. 用戶體驗
- 提供清晰的錯誤訊息
- 支援自動登入狀態檢查
- 優雅處理認證失敗情況

### 3. 維護性
- 統一的錯誤處理機制
- 完整的程式碼註解
- 模組化的認證邏輯

---

*此認證模組展現了從 API Token 到 Session Cookie 的技術演進，提供了更好的用戶體驗和安全性。* 