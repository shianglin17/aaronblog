# API 文件

## 1. API 規範

### 1.1 基本資訊
- 基礎 URL: `https://api.example.com/v1`
- 內容類型: `application/json`
- 認證方式: Bearer Token

### 1.2 通用回應格式
```json
{
    "status": "success",
    "code": 200,
    "message": "操作成功",
    "data": {}
}
```

### 1.3 錯誤碼
- 200: 成功
- 400: 請求錯誤
- 401: 未授權
- 403: 禁止訪問
- 404: 資源不存在
- 500: 伺服器錯誤

## 2. 認證 API

### 2.1 用戶登入
```
POST /auth/login
```

請求參數：
```json
{
    "email": "string",
    "password": "string"
}
```

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "登入成功",
    "data": {
        "token": "string",
        "user": {
            "id": "integer",
            "name": "string",
            "email": "string"
        }
    }
}
```

## 3. 用戶 API

### 3.1 獲取用戶資訊
```
GET /users/{id}
```

請求頭：
```
Authorization: Bearer {token}
```

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "獲取成功",
    "data": {
        "id": "integer",
        "name": "string",
        "email": "string",
        "avatar": "string",
        "created_at": "datetime"
    }
}
```

## 4. 文章 API

### 4.1 獲取文章列表
```
GET /posts
```

查詢參數：
- page: 頁碼
- per_page: 每頁數量
- category: 分類 ID
- tag: 標籤 ID

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "獲取成功",
    "data": {
        "current_page": "integer",
        "data": [
            {
                "id": "integer",
                "title": "string",
                "content": "string",
                "category": {
                    "id": "integer",
                    "name": "string"
                },
                "tags": [
                    {
                        "id": "integer",
                        "name": "string"
                    }
                ],
                "created_at": "datetime"
            }
        ],
        "total": "integer",
        "per_page": "integer"
    }
}
```

### 4.2 創建文章
```
POST /posts
```

請求頭：
```
Authorization: Bearer {token}
Content-Type: application/json
```

請求參數：
```json
{
    "title": "string",
    "content": "string",
    "category_id": "integer",
    "tag_ids": ["integer"]
}
```

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "創建成功",
    "data": {
        "id": "integer",
        "title": "string",
        "content": "string",
        "category": {
            "id": "integer",
            "name": "string"
        },
        "tags": [
            {
                "id": "integer",
                "name": "string"
            }
        ],
        "created_at": "datetime"
    }
}
```

## 5. 評論 API

### 5.1 獲取文章評論
```
GET /posts/{id}/comments
```

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "獲取成功",
    "data": [
        {
            "id": "integer",
            "content": "string",
            "user": {
                "id": "integer",
                "name": "string",
                "avatar": "string"
            },
            "created_at": "datetime"
        }
    ]
}
```

### 5.2 發表評論
```
POST /posts/{id}/comments
```

請求頭：
```
Authorization: Bearer {token}
Content-Type: application/json
```

請求參數：
```json
{
    "content": "string"
}
```

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "評論成功",
    "data": {
        "id": "integer",
        "content": "string",
        "user": {
            "id": "integer",
            "name": "string",
            "avatar": "string"
        },
        "created_at": "datetime"
    }
}
```

## 6. 搜尋 API

### 6.1 全文搜尋
```
GET /search
```

查詢參數：
- q: 搜尋關鍵字
- type: 搜尋類型（post/user）
- page: 頁碼
- per_page: 每頁數量

回應：
```json
{
    "status": "success",
    "code": 200,
    "message": "搜尋成功",
    "data": {
        "current_page": "integer",
        "data": [
            {
                "id": "integer",
                "title": "string",
                "content": "string",
                "type": "string"
            }
        ],
        "total": "integer",
        "per_page": "integer"
    }
}
``` 