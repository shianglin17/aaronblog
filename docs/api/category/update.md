# 更新分類

更新現有的文章分類。

## API 端點

```
PUT /api/admin/categories/{id}
```

## 路徑參數

| 參數名稱 | 類型    | 必填 | 描述    |
|---------|---------|------|---------|
| id      | Integer | 是   | 分類 ID |

## 請求標頭

| 名稱          | 必填 | 描述                                   |
|---------------|------|--------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |
| Authorization | 是   | Bearer token 用於認證                  |
| Content-Type  | 是   | 指定請求格式，固定為 `application/json` |

## 請求參數

| 參數名稱     | 類型   | 必填 | 描述                                   |
|-------------|--------|------|--------------------------------------|
| name        | String | 否   | 分類名稱，最大長度 50 字符              |
| slug        | String | 是   | 分類 Slug，URL 友好的識別符，最大長度 255 字符 |
| description | String | 否   | 分類描述，最大長度 500 字符              |

## 請求示例

```http
PUT /api/admin/categories/4 HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Content-Type: application/json

{
  "name": "程式教學與分享",
  "slug": "programming-tutorials",
  "description": "學習程式設計的教學文章與實用技巧分享"
}
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "分類更新成功",
  "data": {
    "id": 4,
    "name": "程式教學與分享",
    "slug": "programming-tutorials",
    "description": "學習程式設計的教學文章與實用技巧分享"
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
        "分類名稱已存在"
      ],
      "slug": [
        "分類別名已存在"
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

### 錯誤回應 - 分類不存在 (404 Not Found)

```json
{
  "status": "error",
  "code": 404,
  "message": "分類不存在"
}
```

## 備註

- 此 API 需要認證
- 分類名稱是唯一的，不能重複
- 分類 slug 是唯一的，用於前端 URL
- slug 欄位為必填，在更新時必須提供
- 請求參數可以只包含需要更新的欄位，但 slug 為必填