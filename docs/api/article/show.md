# 獲取單篇文章 API

取得單篇文章的詳細資料。

## 請求資訊

**請求方法**
```
GET /api/article/{id}
```

**請求標頭**
```
Accept: application/json
Authorization: Bearer {token}
```

## 請求參數

| 參數 | 類型    | 必填 | 說明              |
|------|---------|------|-------------------|
| id   | integer | 是   | 文章 ID，必須大於 0 |

## 回應範例

### 成功回應 (200)
```json
{
    "status": "success",
    "code": 200,
    "message": "成功",
    "data": {
        "id": 1,
        "title": "文章標題",
        "content": "文章內容",
        "user_id": 1,
        "user_name": "張三",
        "category_id": 2,
        "category_name": "技術分享",
        "created_at": "2024-03-20T12:00:00Z",
        "updated_at": "2024-03-20T12:00:00Z",
        "tags": [
            {
                "id": 1,
                "name": "Laravel",
                "slug": "laravel"
            },
            {
                "id": 2,
                "name": "PHP",
                "slug": "php"
            }
        ]
    }
}
```

## 錯誤回應

### 1. 資源不存在 (404)
```json
{
    "status": "error",
    "code": 404,
    "message": "文章不存在"
}
```

### 2. 無效請求 (400)
```json
{
    "status": "error",
    "code": 400,
    "message": "無效的請求"
}
```

### 3. 未授權 (401)
```json
{
    "status": "error",
    "code": 401,
    "message": "未授權或授權已過期"
}
```

### 4. 伺服器錯誤 (500)
```json
{
    "status": "error",
    "code": 500,
    "message": "伺服器內部錯誤"
}
``` 