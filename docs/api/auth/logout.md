# 登出 API

## 端點

`POST /api/auth/logout`

## 描述

用於用戶（管理員）登出系統。成功登出後，當前 Session 將被清除。

## 請求頭

| 名稱          | 類型   | 必填 | 描述                          |
|--------------|--------|------|------------------------------|
| Accept       | string | 是   | application/json             |
| X-XSRF-TOKEN | string | 是   | CSRF Token                   |

## 請求參數

無

## 回應

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "登出成功",
  "data": null
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

- 此 API 呼叫需要在請求頭中提供有效的 CSRF Token
- 呼叫成功後，當前 Session 將被清除，無法再用於其他 API 呼叫
- 若要重新獲取認證，需要重新登入 