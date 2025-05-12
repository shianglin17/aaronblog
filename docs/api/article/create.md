# 創建文章

## 接口說明

此接口用於創建新文章。

- 請求方式：POST
- 請求地址：`/api/admin/article`
- 需要認證：是

## 請求參數

### Headers

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| Content-Type | 是 | string | application/json |
| Authorization | 是 | string | Bearer {token} |

### Body

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| title | 是 | string | 文章標題，不超過 255 個字符 |
| slug | 是 | string | 文章 URL 標識符，僅允許英文、數字和連字符，不超過 255 個字符，必須唯一 |
| description | 是 | string | 文章摘要，顯示於列表頁及 SEO 描述，建議 150-160 字內 |
| content | 是 | string | 文章完整內容 |
| category_id | 否 | integer | 分類 ID |
| status | 否 | string | 文章狀態，可選值：draft（草稿）、published（已發佈），默認為 draft |
| tags | 否 | array | 標籤 ID 數組 |

## 響應

### 成功響應

```json
{
  "status": "success",
  "code": 201,
  "message": "文章創建成功",
  "data": {
    "id": 1,
    "title": "測試文章",
    "slug": "test-article",
    "description": "這是一篇測試文章的摘要...",
    "content": "這是一篇測試文章的內容...",
    "status": "draft",
    "author": {
      "id": 1,
      "name": "管理員"
    },
    "category": {
      "id": 1,
      "name": "技術分享",
      "slug": "technology-sharing"
    },
    "tags": [
      {
        "id": 1,
        "name": "Laravel",
        "slug": "laravel"
      },
      {
        "id": 2,
        "name": "Vue.js",
        "slug": "vue-js"
      }
    ],
    "created_at": "2023-05-09T12:34:56.000000Z",
    "updated_at": "2023-05-09T12:34:56.000000Z"
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
        "標題不能為空"
      ],
      "slug": [
        "Slug不能為空",
        "Slug只能包含字母、數字、連字符和底線",
        "Slug已存在"
      ],
      "description": [
        "文章描述不能為空"
      ]
    }
  }
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