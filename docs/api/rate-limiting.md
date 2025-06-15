# API Rate Limiting（請求頻率限制）

## 限制規則

| API 類型 | 限制 | 說明 |
|----------|------|------|
| 公開 API | 30 requests/minute | 文章、分類、標籤列表 |
| 登入 API | 5 requests/minute | 防止暴力破解 |
| 管理員 API | 30 requests/minute | 需要認證的管理功能 |

## 限流實現

- **儲存方式**：使用資料庫快取（SQLite cache 資料表）
- **識別方式**：根據 IP 位址追蹤請求次數
- **重置時間**：每分鐘重置計數器

## 回應標頭

每個 API 回應都會包含以下標頭：
```
X-RateLimit-Limit: 30
X-RateLimit-Remaining: 29
```

## 超限錯誤回應

當超過限制時，會回傳 429 狀態碼：

```json
{
    "message": "Too Many Attempts.",
    "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

## 處理建議

1. **檢查回應標頭**：監控剩餘請求次數
2. **實作重試機制**：超限時等待後重試
3. **快取資料**：減少不必要的 API 請求
4. **批次處理**：合併多個請求為單一請求 