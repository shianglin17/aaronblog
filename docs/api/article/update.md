# 更新文章

## 接口說明

此接口用於更新現有文章。

- 請求方式：PUT
- 請求地址：`/api/admin/articles/{id}`
- 需要認證：是

## 請求參數

### Headers

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| Content-Type | 是 | string | application/json |
| X-XSRF-TOKEN | 是 | string | CSRF Token |

### Path Parameters

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| id | 是 | integer | 文章 ID |

### Body

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| title | 否 | string | 文章標題，不超過 255 個字符 |
| description | 否 | string | 文章摘要，顯示於列表頁，建議 150-200 字內 |
| content | 否 | string | 文章完整內容 |
| category_id | 否 | integer | 分類 ID |
| status | 否 | string | 文章狀態，可選值：draft（草稿）、published（已發佈） |
| tags | 否 | array | 標籤 ID 數組 |

## 響應

### 成功響應

```json
{
  "status": "success",
  "code": 200,
  "message": "文章更新成功",
  "data": {
    "id": 1,
    "title": "已更新的文章標題",
    "slug": "updated-article-title",
    "description": "這是已更新的文章摘要...",
    "content": "這是已更新的文章內容...",
    "status": "published",
    "author": {
      "id": 1,
      "name": "管理員"
    },
    "category": {
      "id": 2,
      "name": "心得分享",
      "slug": "experience-sharing"
    },
    "tags": [
      {
        "id": 1,
        "name": "Laravel",
        "slug": "laravel"
      },
      {
        "id": 3,
        "name": "PHP",
        "slug": "php"
      }
    ],
    "created_at": "2023-05-09T12:34:56.000000Z",
    "updated_at": "2023-05-09T13:45:23.000000Z"
  }
}
```

### 錯誤響應

#### 驗證錯誤

```json
{
  "status": "error",
  "code": 422,
  "message": "驗證錯誤",
  "meta": {
    "errors": {
      "title": [
        "標題已存在"
      ]
    }
  }
}
```

#### 文章不存在

```json
{
  "status": "error",
  "code": 404,
  "message": "文章不存在",
  "meta": {}
}
```

#### 未授權錯誤

```json
{
  "status": "error",
  "code": 401,
  "message": "未授權操作",
  "meta": {}
}
``` 