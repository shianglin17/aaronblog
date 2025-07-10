# API 認證

## 認證方式

使用 Session Cookie 認證：
- 認證資訊儲存在 HTTP-only Cookie 中
- 自動由瀏覽器管理，無需手動處理
- 提供最高的安全性保護

## 認證流程

1. **取得 CSRF Token**：
```
   GET /sanctum/csrf-cookie
   ```

2. **登入**：
   ```
   POST /api/auth/login
   Content-Type: application/json
   X-XSRF-TOKEN: {csrf_token}
   
   {
     "email": "user@example.com",
     "password": "password"
   }
   ```

3. **存取受保護的 API**：
   ```
   GET /api/auth/user
   Accept: application/json
   ```

## 請求標頭

所有需要認證的 API 請求都必須包含：
```
Content-Type: application/json
Accept: application/json
X-XSRF-TOKEN: {csrf_token}
```

## 認證錯誤

### 401 未授權
```json
{
    "status": "error",
    "code": 401,
    "message": "Unauthenticated."
}
```

### 403 權限不足
```json
{
    "status": "error",
    "code": 403,
    "message": "This action is unauthorized."
}
``` 