# 創建分類

創建一個新的文章分類。

## API 端點

```
POST /api/admin/categories
```

## 請求標頭

| 名稱          | 必填 | 描述                                   |
|---------------|------|--------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |
| Authorization | 是   | Bearer token 用於認證                  |
| Content-Type  | 是   | 指定請求格式，固定為 `application/json` |

## 請求參數

| 參數名稱     | 類型   | 必填 | 描述                                   |
|-------------|--------|------|--------------------------------------|
| name        | String | 是   | 分類名稱，最大長度 50 字符              |
| slug        | String | 是   | 分類 Slug，URL 友好的識別符，最大長度 255 字符 |
| description | String | 否   | 分類描述，最大長度 500 字符              |

## 請求示例

```http
POST /api/admin/categories HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Content-Type: application/json

{
  "name": "程式教學",
  "slug": "programming-tutorials",
  "description": "學習程式設計的教學文章"
}
```

## 回應格式

### 成功回應 (201 Created)

```json
{
  "status": "success",
  "code": 201,
  "message": "分類創建成功",
  "data": {
    "id": 4,
    "name": "程式教學",
    "slug": "programming-tutorials",
    "description": "學習程式設計的教學文章"
  }
}
```

### 錯誤回應 - 驗證失敗 (422 Unprocessable Entity)

```json
{
  "status": "error",
  "code": 422,
  "message": "驗證失敗",
  "meta": {
    "errors": {
      "name": [
        "分類名稱不能為空"
      ],
      "slug": [
        "分類別名不能為空"
      ]
    }
  }
}
```

### 錯誤回應 - 未授權 (401 Unauthorized)

```json
{
  "status": "error",
  "code": 401,
  "message": "未授權"
}
```

## 備註

- 此 API 需要認證
- 分類名稱是唯一的，不能重複
- 分類 slug 是唯一的，用於前端 URL
- slug 欄位為必填，不會自動生成 