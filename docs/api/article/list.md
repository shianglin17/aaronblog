# 文章列表 API

取得分頁的文章列表資料。

## 請求資訊

**請求方法**
```
GET /api/article/list
```

**請求標頭**
```
Accept: application/json
Authorization: Bearer {token}
```

## 請求參數

| 參數           | 類型    | 必填  | 預設值      | 說明                                                |
|--------------|---------|-------|------------|------------------------------------------------------|
| page         | integer | 否    | 1          | 頁碼，必須大於等於 1                                    |
| per_page     | integer | 否    | 15         | 每頁筆數，範圍 1-100                                   |
| sort_by      | string  | 否    | created_at | 排序欄位，可選值：created_at、updated_at、title          |
| sort_direction| string  | 否    | desc       | 排序方向，可選值：asc（升序）、desc（降序）                  |
| search       | string  | 否    | ''         | 搜尋關鍵字，最多 255 字元                               |

## 回應範例

### 成功回應 (200)
```json
{
    "status": "success",
    "code": 200,
    "message": "成功",
    "data": [
        {
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
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "total_pages": 7,
            "total_items": 100,
            "per_page": 15
        }
    }
}
```

## 錯誤回應

### 1. 驗證錯誤 (422)
```json
{
    "status": "error",
    "code": 422,
    "message": "驗證失敗",
    "errors": {
        "page": ["頁碼必須大於等於 1"],
        "per_page": ["每頁筆數必須介於 1 到 100 之間"],
        "sort_by": ["排序欄位不在允許的選項中"],
        "sort_direction": ["排序方向必須是 asc 或 desc"],
        "search": ["搜尋關鍵字不可超過 255 字元"]
    }
}
```

### 2. 未授權 (401)
```json
{
    "status": "error",
    "code": 401,
    "message": "未授權或授權已過期"
}
```

### 3. 伺服器錯誤 (500)
```json
{
    "status": "error",
    "code": 500,
    "message": "伺服器內部錯誤"
}
``` 