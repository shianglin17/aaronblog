# Aaron Blog API 通用規範

## API 版本
- 當前版本：v1
- 基礎路徑：`/api`

## 請求格式
- 所有請求必須使用 HTTPS
- Content-Type: application/json
- Accept: application/json

## 認證方式
- Bearer Token 認證
- Token 需要在 Header 中加入：`Authorization: Bearer {token}`

## 通用回應格式

### 成功回應
```json
{
    "status": "success",
    "code": 200,
    "message": "成功",
    "data": null,
    "meta": null
}
```

### 分頁資料格式
```json
{
    "status": "success",
    "code": 200,
    "message": "成功",
    "data": [...],
    "meta": {
        "pagination": {
            "current_page": 1,
            "total_pages": 10,
            "total_items": 100,
            "per_page": 10
        }
    }
}
```

### 錯誤回應
```json
{
    "status": "error",
    "code": 400,
    "message": "錯誤訊息"
}
```

### 驗證錯誤回應
```json
{
    "status": "error",
    "code": 422,
    "message": "驗證錯誤",
    "errors": {
        "field": ["錯誤訊息"]
    }
}
```

## HTTP 狀態碼
- 200：請求成功
- 400：請求錯誤
- 401：未認證
- 403：權限不足
- 404：資源不存在
- 422：驗證錯誤
- 500：伺服器錯誤 