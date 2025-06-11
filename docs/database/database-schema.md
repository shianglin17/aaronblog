# 資料庫設計文件

## 文章系統資料表結構

### 1. articles（文章表）✅
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

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)
- UNIQUE KEY (`title`)
- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
- FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL

### 2. categories（分類表）✅
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| slug       | varchar(255)| not null, unique        | slug        |
| name       | varchar(50) | not null, unique        | 分類名稱      |
| description| text        | not null                | 分類描述      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)
- UNIQUE KEY (`name`)

### 3. tags（標籤表）✅
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| slug       | varchar(255)| not null, unique        | slug        |
| name       | varchar(50) | not null                | 標籤名稱      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)

### 4. article_tag（文章標籤關聯表）✅
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

## 認證系統資料表結構

### 5. personal_access_tokens（Sanctum API Token 表）✅
| 欄位名稱        | 型別          | 屬性                      | 說明              |
|----------------|--------------|--------------------------|------------------|
| id             | bigint      | unsigned, auto_increment | 主鍵 ID           |
| tokenable_type | varchar(255)| not null                | 令牌關聯對象的類型   |
| tokenable_id   | bigint      | unsigned, not null      | 令牌關聯對象的 ID   |
| name           | varchar(255)| not null                | 令牌名稱           |
| token          | varchar(64) | not null, unique        | 令牌雜湊值         |
| abilities      | text        | nullable                | 令牌能力（權限）     |
| last_used_at   | timestamp   | nullable                | 上次使用時間        |
| expires_at     | timestamp   | nullable                | 過期時間            |
| created_at     | timestamp   | nullable                | 建立時間            |
| updated_at     | timestamp   | nullable                | 更新時間            |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`token`)
- KEY (`tokenable_type`, `tokenable_id`)

### 6. users（使用者表）✅
| 欄位名稱           | 型別          | 屬性                      | 說明             |
|-------------------|--------------|--------------------------|-----------------|
| id                | bigint      | unsigned, auto_increment | 主鍵 ID          |
| name              | varchar(255)| not null                | 使用者名稱        |
| email             | varchar(255)| not null, unique        | 電子郵件          |
| email_verified_at | timestamp   | nullable                | 電子郵件驗證時間    |
| password          | varchar(255)| not null                | 密碼雜湊值        |
| remember_token    | varchar(100)| nullable                | 記住我令牌        |
| created_at        | timestamp   | nullable                | 建立時間          |
| updated_at        | timestamp   | nullable                | 更新時間          |

索引：
- PRIMARY KEY (`id`)
- UNIQUE KEY (`email`)

## 系統資料表結構

### 7. sessions（Session 表）✅
| 欄位名稱      | 型別           | 屬性              | 說明            |
|--------------|---------------|------------------|----------------|
| id           | varchar(255)  | not null         | Session ID     |
| user_id      | bigint       | unsigned, nullable| 使用者 ID        |
| ip_address   | varchar(45)   | nullable         | IP 地址         |
| user_agent   | text          | nullable         | 使用者代理字串    |
| payload      | longtext      | not null         | Session 資料    |
| last_activity| int           | not null         | 最後活動時間      |

索引：
- PRIMARY KEY (`id`)
- KEY (`user_id`)
- KEY (`last_activity`)

### 8. cache（快取表）✅
| 欄位名稱      | 型別           | 屬性              | 說明            |
|--------------|---------------|------------------|----------------|
| key          | varchar(255)  | not null         | 快取鍵值         |
| value        | mediumtext    | not null         | 快取資料         |
| expiration   | int           | not null         | 過期時間         |

索引：
- PRIMARY KEY (`key`)

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

4. **使用者與 API Token（一對多）**
   - 一個使用者可以有多個 API Token
   - 每個 Token 只屬於一個使用者
   - 使用多態關聯 `tokenable_type` 和 `tokenable_id` 字段

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

5. **刪除策略**
   - 使用硬刪除（Hard Delete）
   - 刪除記錄時會直接從資料庫中移除
   - 透過外鍵約束處理關聯資料的清理
   - 建議在刪除前進行資料備份 