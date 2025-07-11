# 登入 API

## 端點

`POST /api/auth/login`

## 描述

用於用戶（管理員）登入系統。成功登入後將建立 Session Cookie 並返回用戶資訊。

## 請求頭

| 名稱          | 類型   | 必填 | 描述                 |
|--------------|--------|------|---------------------|
| Accept       | string | 是   | application/json    |
| Content-Type | string | 是   | application/json    |
| X-XSRF-TOKEN | string | 是   | CSRF Token（從 /sanctum/csrf-cookie 獲取）|

## 請求參數

| 參數名稱   | 類型   | 必填 | 描述                   |
|-----------|--------|------|----------------------|
| email     | string | 是   | 用戶電子郵件地址         |
| password  | string | 是   | 用戶密碼               |

## 請求範例

```json
{
  "email": "leishianglin@gmail.com",
  "password": "password123"
}
```

## 回應

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "登入成功",
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

### 驗證失敗 (401 Unauthorized)

```json
{
  "status": "error",
  "code": 401,
  "message": "提供的憑證不正確。",
  "data": null
}
```

### 格式錯誤 (422 Unprocessable Entity)

```json
{
  "status": "error",
  "code": 422,
  "message": "給定的數據無效。",
  "data": null,
  "meta": {
  "errors": {
    "email": [
      "電子郵件欄位為必填項。"
    ],
    "password": [
      "密碼欄位為必填項。"
    ]
    }
  }
}
```

## 備註

- 成功登入後系統會建立 Session Cookie，瀏覽器會自動管理
- 所有需要認證的 API 呼叫都需要在請求頭中加入 `X-XSRF-TOKEN: {csrf_token}`
- 在登入前需要先呼叫 `/sanctum/csrf-cookie` 獲取 CSRF Token
- Session 有效期由系統設定決定 