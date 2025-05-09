# Laravel Sanctum 認證流程詳解

## 概述

本文檔解釋 Laravel Sanctum 認證系統的工作原理和使用方式，適用於本項目的 API 認證。

## 認證流程圖（簡化版）

```
┌─────────────┐     ┌─────────────┐     ┌───────────────┐
│  客戶端     │     │ AuthController│     │ Sanctum Token │
│  (前端)     │────▶│   login()    │────▶│     創建       │
└─────────────┘     └─────────────┘     └───────┬───────┘
                                                 │
                                                 ▼
┌─────────────┐     ┌─────────────┐
│  客戶端     │     │   Response   │
│  (獲得token) │◀────│ (user+token) │
└─────────────┘     └─────────────┘
```

## 登入流程

### 1. 客戶端發送登入請求

```
POST /api/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

### 2. 伺服器驗證憑證

AuthController 驗證用戶憑證，成功後創建 Sanctum token：

```php
// 簡化示例
public function login(LoginRequest $request)
{
    $request->authenticate();  // 驗證憑證
    
    $user = Auth::user();  // 獲取已登入的用戶
    $token = $user->createToken('api-token')->plainTextToken;  // 建立 Sanctum token
    
    return response()->json([
        'status' => 'success',
        'code' => 200,
        'message' => '登入成功',
        'data' => [
            'user' => $user,
            'token' => $token
        ]
    ]);
}
```

### 3. 客戶端存儲並使用 Token

客戶端收到 Token 後，使用 Bearer 方式在後續 API 請求中提供：

```
GET /api/articles
Authorization: Bearer 1|a1b2c3d4e5f6g7h8i9j0...
```

## Token 儲存與處理

- Sanctum tokens 儲存在 `personal_access_tokens` 表中
- 每個 token 都與特定用戶關聯
- Tokens 可設定過期時間（目前系統未設定自動過期）

## 登出流程

使用者登出時，系統撤銷 token：

```php
// 簡化示例
public function logout(Request $request)
{
    // 移除當前 token
    $request->user()->currentAccessToken()->delete();
    
    return response()->json([
        'status' => 'success',
        'code' => 200,
        'message' => '登出成功'
    ]);
}
```

## 實用提示

### 1. 檢查 Token 是否有效

可使用 `/api/auth/user` API 端點測試 token 是否有效：

```
GET /api/auth/user
Authorization: Bearer 1|a1b2c3d4e5f6g7h8i9j0...
```

成功回應表示 token 有效，否則需要重新登入。

### 2. Token 格式

Sanctum tokens 格式為 `{id}|{hash}`，例如 `1|a1b2c3d4e5...`：
- id: token 在資料庫中的 ID
- hash: 用於驗證的隨機字串（僅保存雜湊值）

### 3. 安全考量

- 始終使用 HTTPS 傳輸 tokens
- 請勿將 tokens 存儲在公開處
- 不再使用時及時登出，撤銷 token

## 故障排除

1. **Token 無效**：重新登入獲取新 token
2. **Token 過期**：本系統目前未設定 token 自動過期
3. **401 未授權**：檢查 token 傳輸格式，確保使用 `Bearer {token}` 