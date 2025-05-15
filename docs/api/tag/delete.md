# 刪除標籤

刪除現有的文章標籤。

## API 端點

```
DELETE /api/admin/tags/{id}
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

## 請求示例

```http
DELETE /api/admin/tags/7 HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "標籤刪除成功"
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
- 刪除標籤會同時解除該標籤與所有文章的關聯，但不會刪除關聯的文章
- 刪除後的標籤會被軟刪除（soft delete），在資料庫中仍然存在但不會出現在查詢結果中 