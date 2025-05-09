# 刪除文章

## 接口說明

此接口用於刪除文章，實際是軟刪除，將文章標記為已刪除。

- 請求方式：DELETE
- 請求地址：`/api/admin/article/{id}`
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
  "message": "文章刪除成功",
  "data": null
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