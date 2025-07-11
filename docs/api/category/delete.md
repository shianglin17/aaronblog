# 刪除分類

刪除現有的文章分類。

## API 端點

```
DELETE /api/admin/categories/{id}
```

## 路徑參數

| 參數名稱 | 類型    | 必填 | 描述    |
|---------|---------|------|---------|
| id      | Integer | 是   | 分類 ID |

## 請求標頭

| 名稱          | 必填 | 描述                                   |
|---------------|------|--------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |
| X-XSRF-TOKEN  | 是   | CSRF Token 用於認證                 |

## 請求示例

```http
DELETE /api/admin/categories/4 HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
X-XSRF-TOKEN: csrf_token_value
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "分類刪除成功"
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
- 刪除分類會將關聯的文章的分類設為 null，但不會刪除關聯的文章
- 刪除後的分類會被永久刪除，從資料庫中完全移除 