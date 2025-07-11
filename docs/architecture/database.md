# 資料庫架構

## 概述

本專案目前僅使用 SQLite 作為資料庫，所有資料表結構與關聯均以 Laravel migration 為準。未來如更換資料庫，將以現有結構為基礎調整。

## 資料表關聯圖

```
┌───────────┐      ┌───────────┐      ┌───────────┐
│   users   │──1:n─▶  articles ◀─n:1──│ categories│
└───────────┘      └─────┬─────┘      └───────────┘
                         │
                         │ n:m
                         ▼
                   ┌───────────┐
                   │    tags   │
                   └───────────┘
```

## 資料表結構

### 1. articles（文章表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | integer      | primary key, auto-increment | 主鍵 ID      |
| slug       | string(255)  | not null, unique        | slug        |
| title      | string(255)  | not null, unique        | 文章標題      |
| description| string(255)  | not null                | 文章摘要      |
| content    | text         | not null                | 文章內容      |
| status     | string(20)   | default 'draft'         | 狀態         |
| user_id    | integer      | nullable, foreign key   | 作者 ID      |
| category_id| integer      | nullable, foreign key   | 分類 ID      |
| created_at | timestamp    | nullable                | 建立時間      |
| updated_at | timestamp    | nullable                | 更新時間      |

### 2. categories（分類表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | integer      | primary key, auto-increment | 主鍵 ID      |
| slug       | string(255)  | not null, unique        | slug        |
| name       | string(50)   | not null, unique        | 分類名稱      |
| description| text         | not null                | 分類描述      |
| created_at | timestamp    | nullable                | 建立時間      |
| updated_at | timestamp    | nullable                | 更新時間      |

### 3. tags（標籤表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | integer      | primary key, auto-increment | 主鍵 ID      |
| slug       | string(255)  | not null, unique        | slug        |
| name       | string(50)   | not null                | 標籤名稱      |
| created_at | timestamp    | nullable                | 建立時間      |
| updated_at | timestamp    | nullable                | 更新時間      |

### 4. article_tag（文章標籤關聯表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| article_id | integer      | foreign key              | 文章 ID      |
| tag_id     | integer      | foreign key              | 標籤 ID      |
| created_at | timestamp    | nullable                | 建立時間      |
| updated_at | timestamp    | nullable                | 更新時間      |

### 5. users（使用者表）
| 欄位名稱           | 型別          | 屬性                      | 說明             |
|-------------------|--------------|--------------------------|-----------------|
| id                | integer      | primary key, auto-increment | 主鍵 ID          |
| name              | string(255)  | not null                | 使用者名稱        |
| email             | string(255)  | not null, unique        | 電子郵件          |
| email_verified_at | timestamp    | nullable                | 電子郵件驗證時間    |
| password          | string(255)  | not null                | 密碼雜湊值        |
| remember_token    | string(100)  | nullable                | 記住我令牌        |
| created_at        | timestamp    | nullable                | 建立時間          |
| updated_at        | timestamp    | nullable                | 更新時間          |

### 6. personal_access_tokens（API Token 表）
| 欄位名稱        | 型別          | 屬性                      | 說明              |
|----------------|--------------|--------------------------|------------------|
| id             | integer      | primary key, auto-increment | 主鍵 ID           |
| tokenable_type | string(255)  | not null                | 關聯對象類型        |
| tokenable_id   | integer      | not null                | 關聯對象 ID         |
| name           | string(255)  | not null                | 令牌名稱           |
| token          | string(64)   | not null, unique        | 令牌雜湊值         |
| abilities      | text         | nullable                | 權限               |
| last_used_at   | timestamp    | nullable                | 上次使用時間        |
| expires_at     | timestamp    | nullable                | 過期時間            |
| created_at     | timestamp    | nullable                | 建立時間            |
| updated_at     | timestamp    | nullable                | 更新時間            |

### 7. sessions（Session 表）
| 欄位名稱      | 型別           | 屬性              | 說明            |
|--------------|---------------|------------------|----------------|
| id           | string(255)   | primary key      | Session ID     |
| user_id      | integer       | nullable, index  | 使用者 ID        |
| ip_address   | string(45)    | nullable         | IP 地址         |
| user_agent   | text          | nullable         | 使用者代理字串    |
| payload      | text          | not null         | Session 資料    |
| last_activity| integer       | not null, index  | 最後活動時間      |

### 8. cache（快取表）
| 欄位名稱      | 型別           | 屬性              | 說明            |
|--------------|---------------|------------------|----------------|
| key          | string(255)   | primary key      | 快取鍵值         |
| value        | text          | not null         | 快取資料         |
| expiration   | integer       | not null         | 過期時間         |

## 資料表關聯說明

- 文章（articles）與使用者（users）：多對一，user_id 外鍵，刪除使用者時 user_id 設為 NULL。
- 文章（articles）與分類（categories）：多對一，category_id 外鍵，刪除分類時 category_id 設為 NULL。
- 文章（articles）與標籤（tags）：多對多，透過 article_tag 關聯表，刪除文章或標籤時自動移除關聯。
- 使用者（users）與 Session（sessions）：一對多，Session 資料存儲於 Redis 中。

## 備份策略

- 直接備份 storage/app/database 目錄下的 SQLite 檔案即可。
- 恢復時將備份檔案覆蓋回原目錄。

## 開發工具

- Laravel Migrations：管理資料表結構
- Artisan 指令：php artisan migrate, php artisan migrate:rollback
- Eloquent ORM：資料存取

## 版本控制

- 所有資料表結構變更均透過 Laravel migration 進行版本控制。

``` 