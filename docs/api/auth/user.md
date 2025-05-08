# 獲取當前用戶資訊 API

## 端點

`GET /api/auth/user`

## 描述

獲取當前已認證用戶（管理員）的資訊。

## 請求頭

| 名稱          | 類型   | 必填 | 描述                          |
|--------------|--------|------|------------------------------|
| Accept       | string | 是   | application/json             |
| Authorization| string | 是   | Bearer {token}               |

## 請求參數

無

## 回應

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "成功",
  "data": {
    "user": {
      "id": 1,
      "name": "Aaron",
      "email": "leishianglin@gmail.com",
      "email_verified_at": "2024-05-10T10:00:00.000000Z",
      "created_at": "2024-05-10T10:00:00.000000Z",
      "updated_at": "2024-05-10T10:00:00.000000Z"
    }
  }
}
```

### 未認證 (401 Unauthorized)

```json
{
  "status": "error",
  "code": 401,
  "message": "未授權",
  "data": null
}
```

## 備註

- 此 API 呼叫需要在請求頭中提供有效的 Bearer Token
- 用於前端獲取當前登入用戶的基本資訊
- 可用於驗證用戶是否已登入 