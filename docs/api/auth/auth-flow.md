# Laravel Sanctum Session Cookie 認證流程詳解

## 概述

本文檔解釋 Laravel Sanctum Session Cookie 認證系統的工作原理和使用方式，適用於本項目的 API 認證。本系統已從 API Token 認證遷移至 Session Cookie 認證。

## 認證流程圖

```
┌─────────────┐     ┌─────────────┐     ┌───────────────┐
│  客戶端     │     │ AuthController│     │ Session Cookie │
│  (前端)     │────▶│   login()    │────▶│     建立       │
└─────────────┘     └─────────────┘     └───────┬───────┘
                                                 │
                                                 ▼
┌─────────────┐     ┌─────────────┐
│  客戶端     │     │   Response   │
│  (獲得session)│◀────│ (user data) │
└─────────────┘     └─────────────┘
```

## 認證流程

### 1. 獲取 CSRF Token

首先客戶端必須獲取 CSRF Token：

```
GET /sanctum/csrf-cookie
```

### 2. 客戶端發送登入請求

```
POST /api/auth/login
Content-Type: application/json
X-XSRF-TOKEN: {csrf_token}

{
    "email": "admin@example.com",
    "password": "password"
}
```

### 3. 伺服器驗證憑證

AuthController 驗證用戶憑證，成功後建立 Session：

```php
// 簡化示例
public function login(LoginRequest $request)
{
    $credentials = $request->validated();
    
    if (!Auth::guard('web')->attempt($credentials, true)) {
        throw ValidationException::withMessages([
            'email' => ['提供的憑證不正確。'],
        ]);
    }
    
    $request->session()->regenerate();
    $user = Auth::guard('web')->user();
    
    return response()->json([
        'status' => 'success',
        'code' => 200,
        'message' => '登入成功',
        'data' => [
            'user' => $user
        ]
    ]);
}
```

### 4. 客戶端使用 Session Cookie

客戶端收到 Session Cookie 後，瀏覽器會自動在後續請求中提供：

```
GET /api/auth/user
Accept: application/json
X-XSRF-TOKEN: {csrf_token}
```

## Session 儲存與處理

- Session 資料儲存在 Redis 中
- 每個 Session 都與特定用戶關聯
- Session 會自動過期（根據系統設定）
- 使用 HTTP-only Cookie 確保安全性

## 登出流程

使用者登出時，系統清除 Session：

```php
// 簡化示例
public function logout(Request $request)
{
    Auth::guard('web')->logout();
    
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return response()->json([
        'status' => 'success',
        'code' => 200,
        'message' => '登出成功'
    ]);
}
```

## 實用提示

### 1. 檢查認證狀態

可使用 `/api/auth/user` API 端點測試認證狀態：

```
GET /api/auth/user
Accept: application/json
X-XSRF-TOKEN: {csrf_token}
```

成功回應表示用戶已認證，否則需要重新登入。

### 2. CSRF Token 管理

- 前端應自動從 Cookie 或 meta tag 獲取 CSRF Token
- 所有修改性請求都需要提供 CSRF Token
- 認證錯誤時應重新獲取 CSRF Token

### 3. 安全考量

- 始終使用 HTTPS 傳輸認證資料
- Session Cookie 設定為 HTTP-only
- 自動處理 CSRF 保護
- Session 定期輪轉防止 Session fixation

## 故障排除

1. **認證失敗**：檢查 CSRF Token 是否正確
2. **419 CSRF 錯誤**：重新獲取 CSRF Token
3. **401 未授權**：檢查 Session 是否有效，可能需要重新登入
4. **Session 過期**：自動重定向到登入頁面

## 與 API Token 的差異

### 優勢
- 更好的用戶體驗（無需手動管理 Token）
- 自動 CSRF 保護
- 與傳統 Web 應用整合更佳
- 瀏覽器自動管理 Cookie

### 注意事項
- 需要處理 CSRF Token
- 跨域請求需要正確配置 CORS
- 需要支援 Cookie 的客戶端 