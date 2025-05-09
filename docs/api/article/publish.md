# 發佈文章

## 接口說明

此接口用於將文章狀態設為已發佈。

- 請求方式：PATCH
- 請求地址：`/api/admin/article/{id}/publish`
- 需要認證：是

## 請求參數

### Headers

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| Authorization | 是 | string | Bearer {token} |

### Path Parameters

| 參數名 | 必填 | 類型 | 說明 |
| --- | --- | --- | --- |
| id | 是 | integer | 文章 ID |

## 響應

### 成功響應

```json
{
  "status": "success",
  "code": 200,
  "message": "文章已發佈",
  "data": {
    "id": 1,
    "title": "測試文章",
    "slug": "test-article",
    "content": "這是一篇測試文章的內容...",
    "status": "published",
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
      }
    ],
    "created_at": "2023-05-09T12:34:56.000000Z",
    "updated_at": "2023-05-09T14:23:45.000000Z"
  }
}
```

### 錯誤響應

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