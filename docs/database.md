# 資料庫設計文件

## 文章系統資料表結構

### 1. articles（文章表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| slug       | varchar(255)| not null, unique        | slug        |
| title      | varchar(255)| not null, unique        | 文章標題      |
| content    | text        | not null                | 文章內容      |
| user_id    | bigint      | unsigned, nullable      | 作者 ID      |
| category_id| bigint      | unsigned, nullable      | 分類 ID      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |
| deleted_at | timestamp   | nullable                | 軟刪除時間    |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)
- UNIQUE KEY (`title`)
- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
- FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL

### 2. categories（分類表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| slug       | varchar(255)| not null, unique        | slug        |
| name       | varchar(50) | not null, unique        | 分類名稱      |
| description| text        | not null                | 分類描述      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |
| deleted_at | timestamp   | nullable                | 軟刪除時間    |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)
- UNIQUE KEY (`name`)

### 3. tags（標籤表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| slug       | varchar(255)| not null, unique        | slug        |
| name       | varchar(50) | not null                | 標籤名稱      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |
| deleted_at | timestamp   | nullable                | 軟刪除時間    |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)

### 4. article_tag（文章標籤關聯表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| article_id | bigint      | unsigned                | 文章 ID      |
| tag_id     | bigint      | unsigned                | 標籤 ID      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`article_id`, `tag_id`)
- FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
- FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE

## 資料表關聯說明

1. **文章與使用者（多對一）**
   - 一篇文章只能屬於一個使用者
   - 一個使用者可以有多篇文章
   - 使用 `user_id` 作為外鍵
   - 當使用者被刪除時，相關文章的 `user_id` 設為 NULL

2. **文章與分類（多對一）**
   - 一篇文章只能屬於一個分類
   - 一個分類可以有多篇文章
   - 使用 `category_id` 作為外鍵
   - 當分類被刪除時，相關文章的 `category_id` 設為 NULL

3. **文章與標籤（多對多）**
   - 一篇文章可以有多個標籤
   - 一個標籤可以屬於多篇文章
   - 使用 `article_tag` 關聯表建立多對多關係
   - 當文章或標籤被刪除時，相關的關聯記錄也會被刪除

## 注意事項

1. **唯一性約束**
   - 文章的 slug 和標題必須唯一
   - 分類的 slug 和名稱必須唯一
   - 標籤的 slug 必須唯一

2. **外鍵約束**
   - 使用者被刪除時，相關文章的 user_id 設為 NULL
   - 分類被刪除時，相關文章的 category_id 設為 NULL
   - 文章或標籤被刪除時，關聯表的記錄會被級聯刪除

3. **欄位長度**
   - slug 長度限制為 255 字元
   - 文章標題長度限制為 255 字元
   - 分類和標籤名稱限制為 50 字元
   - 文章內容和分類描述使用 text 型別，可存儲較長文字

4. **時間戳記**
   - 所有表都包含 `created_at` 和 `updated_at` 時間戳記
   - 用於追蹤記錄的建立和更新時間

5. **軟刪除機制**
   - 文章、分類和標籤都實作了軟刪除機制
   - 使用 `deleted_at` 欄位標記刪除時間
   - 當記錄被「刪除」時，實際上只是設置 `deleted_at` 時間戳記
   - 預設的查詢會自動排除已軟刪除的記錄
   - 可以使用特定方法查詢或恢復已軟刪除的記錄：
     - `withTrashed()`: 包含已刪除的記錄
     - `onlyTrashed()`: 只查詢已刪除的記錄
     - `restore()`: 恢復已刪除的記錄
     - `forceDelete()`: 永久刪除記錄 