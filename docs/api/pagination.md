# API 分頁規範

## 分頁參數

| 參數名 | 類型 | 必填 | 預設值 | 說明 |
|--------|------|------|---------|------|
| page | integer | 否 | 1 | 頁碼，從 1 開始 |
| per_page | integer | 否 | 15 | 每頁筆數，最大 100 |

## 分頁回應格式

```json
{
    "status": "success",
    "code": 200,
    "message": "成功",
    "data": [
        {
            "id": 1,
            "title": "文章標題"
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "total_pages": 10,
            "total_items": 100,
            "per_page": 15
        }
    }
}
```

## 分頁欄位說明

| 欄位 | 說明 |
|------|------|
| current_page | 目前頁碼 |
| total_pages | 總頁數 |
| total_items | 總筆數 |
| per_page | 每頁筆數 |

## 使用範例

```bash
# 取得第 2 頁，每頁 20 筆
GET /api/articles?page=2&per_page=20
``` 