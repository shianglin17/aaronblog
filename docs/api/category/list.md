# 獲取所有分類

獲取系統中所有可用的文章分類。

## API 端點

```
GET /api/categories
```

## 請求參數

無

## 請求標頭

| 名稱          | 必填 | 描述                                  |
|---------------|------|---------------------------------------|
| Accept        | 是   | 指定回應格式，固定為 `application/json` |

## 請求示例

```http
GET /api/categories HTTP/1.1
Host: api.aaronblog.com
Accept: application/json
```

## 回應格式

### 成功回應 (200 OK)

```json
{
  "code": 200,
  "message": "所有分類",
  "data": [
    {
      "id": 1,
      "name": "技術分享",
      "slug": "tech-sharing",
      "description": "關於程式設計和技術的文章"
    },
    {
      "id": 2,
      "name": "生活記事",
      "slug": "life-notes",
      "description": "日常生活和隨筆"
    },
    {
      "id": 3,
      "name": "書籍推薦",
      "slug": "book-recommendations",
      "description": "好書分享和閱讀心得"
    }
  ]
}
```

### 欄位說明

**分類物件**：

| 欄位名稱 | 類型 | 描述 |
|----------|------|------|
| id | Integer | 分類 ID |
| name | String | 分類名稱 |
| slug | String | 分類 Slug（URL 友好的識別符） |
| description | String | 分類描述（可能為 null） |

## 備註

- 此 API 不需要認證，所有用戶都可以訪問
- 分類數據通常用於前台文章篩選
- slug 欄位可用於前台路由和 URL 顯示 