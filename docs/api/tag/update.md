# 更新標籤

更新現有的文章標籤。

## API 端點

```
PUT /api/admin/tags/{id}
```

## 路徑參數

| 參數名稱 | 類型    | 必填 | 描述    |
|---------|---------|------|---------|
| id      | Integer | 是   | 標籤 ID |

## 請求標頭

| 名稱          | 必填 | 描述                                   |
|---------------|------|--------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |
| Authorization | 是   | Bearer token 用於認證                  |
| Content-Type  | 是   | 指定請求格式，固定為 `application/json` |

## 請求參數

| 參數名稱     | 類型   | 必填 | 描述                                   |
|-------------|--------|------|--------------------------------------|
| name        | String | 否   | 標籤名稱，最大長度 50 字符              |
| slug        | String | 否   | 標籤 Slug，URL 友好的識別符，最大長度 255 字符。如果不提供但更新了 name，系統會根據 name 自動生成 |

## 請求示例

```http
PUT /api/admin/tags/7 HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Content-Type: application/json

{
  "name": "雲端科技",
  "slug": "cloud-tech"
}
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "標籤更新成功",
  "data": {
    "id": 7,
    "name": "雲端科技",
    "slug": "cloud-tech"
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
        "標籤名稱已存在"
      ],
      "slug": [
        "Slug已存在"
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

### 錯誤回應 - 標籤不存在 (404 Not Found)

```json
{
  "status": "error",
  "code": 404,
  "message": "標籤不存在"
}
```

## 備註

- 此 API 需要認證
- 標籤名稱是唯一的，不能重複
- 標籤 slug 是唯一的，用於前端 URL
- 如果不提供 slug 但提供了 name，系統會根據名稱自動生成 slug
- 請求參數可以只包含需要更新的欄位 