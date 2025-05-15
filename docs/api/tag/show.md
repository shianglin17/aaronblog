# 獲取標籤詳情

獲取指定標籤的詳細資訊。

## API 端點

```
GET /api/tags/{id}
```

## 路徑參數

| 參數名稱 | 類型    | 必填 | 描述    |
|---------|---------|------|---------|
| id      | Integer | 是   | 標籤 ID |

## 請求標頭

| 名稱          | 必填 | 描述                                   |
|---------------|------|--------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |

## 請求示例

```http
GET /api/tags/1 HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "標籤詳情",
  "data": {
    "id": 1,
    "name": "Laravel",
    "slug": "laravel"
  }
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

- 此 API 不需要認證，所有用戶都可以訪問
- 此 API 通常用於前端顯示特定標籤的詳細信息或編輯表單的初始化 