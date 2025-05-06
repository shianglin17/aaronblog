# 登出 API

## 端點

`POST /api/auth/logout`

## 描述

用於用戶（管理員）登出系統。成功登出後，當前使用的 API Token 將被廢除。

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
  "message": "登出成功"
}
```

### 未認證 (401 Unauthorized)

```json
{
  "message": "Unauthenticated."
}
```

## 備註

- 此 API 呼叫需要在請求頭中提供有效的 Bearer Token
- 呼叫成功後，提供的 Token 將被廢除，無法再用於其他 API 呼叫
- 若要重新獲取 Token，需要重新登入 