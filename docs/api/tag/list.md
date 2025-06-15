# 獲取所有標籤

獲取系統中所有可用的文章標籤。

## API 端點

```
GET /api/tags
```

## 請求參數

無

## 請求標頭

| 名稱          | 必填 | 描述                                  |
|---------------|------|---------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |

## 請求示例

```http
GET /api/tags HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "status": "success",
  "code": 200,
  "message": "所有標籤",
  "data": [
    {
      "id": 1,
      "name": "Laravel",
      "slug": "laravel",
      "created_at": "2024-03-20T12:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Vue.js",
      "slug": "vuejs",
      "created_at": "2024-03-20T12:00:00.000000Z"
    },
    {
      "id": 3,
      "name": "PHP",
      "slug": "php",
      "created_at": "2024-03-20T12:00:00.000000Z"
    },
    {
      "id": 4,
      "name": "JavaScript",
      "slug": "javascript",
      "created_at": "2024-03-20T12:00:00.000000Z"
    },
    {
      "id": 5,
      "name": "資料庫",
      "slug": "database",
      "created_at": "2024-03-20T12:00:00.000000Z"
    }
  ]
}
```

### 欄位說明

**標籤物件**：

| 欄位名稱 | 類型 | 描述 |
|----------|------|------|
| id | Integer | 標籤 ID |
| name | String | 標籤名稱 |
| slug | String | 標籤 Slug（URL 友好的識別符） |
| created_at | String | 建立時間（ISO 8601 格式） |

## 備註

- 此 API 不需要認證，所有用戶都可以訪問
- 標籤數據通常用於前台文章篩選和標籤雲顯示
- 一篇文章可以有多個標籤 