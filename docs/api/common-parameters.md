# API 通用參數

## 排序參數

| 參數名 | 類型 | 必填 | 預設值 | 說明 |
|--------|------|------|---------|------|
| sort_by | string | 否 | created_at | 排序欄位 |
| sort_direction | string | 否 | desc | 排序方向（asc/desc） |

### 可用排序欄位

- `created_at` - 建立時間
- `id` - ID
- `name` - 名稱（適用於分類、標籤）
- `title` - 標題（適用於文章）

## 搜尋參數

| 參數名 | 類型 | 必填 | 預設值 | 說明 |
|--------|------|------|---------|------|
| search | string | 否 | '' | 搜尋關鍵字，最多 255 字元 |

## 日期時間格式

所有日期時間欄位統一使用 ISO 8601 格式：
- 格式：`YYYY-MM-DDTHH:mm:ssZ`
- 時區：UTC
- 範例：`2024-03-20T12:00:00Z`

## 使用範例

```bash
# 搜尋包含 "Laravel" 的文章，按建立時間升序排列
GET /api/articles?search=Laravel&sort_by=created_at&sort_direction=asc

# 搜尋分類，按名稱排序
GET /api/categories?search=技術&sort_by=name&sort_direction=asc
``` 