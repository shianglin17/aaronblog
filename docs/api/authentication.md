# API 認證

## 認證方式

使用 Bearer Token 認證：
```
Authorization: Bearer {token}
```

## Token 管理

- **有效期**：24 小時
- **取得方式**：透過 `/api/auth/login` 登入取得
- **刷新方式**：Token 過期後需重新登入

## 請求標頭

所有需要認證的 API 請求都必須包含：
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {your_token}
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