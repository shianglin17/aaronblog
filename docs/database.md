# 資料庫設計文件

## 文章系統資料表結構

### 1. articles（文章表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| title      | varchar(255)| not null                | 文章標題      |
| content    | text        | not null                | 文章內容      |
| authors    | varchar(100)| not null                | 作者名稱      |
| category_id| bigint      | unsigned, nullable      | 分類 ID      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`id`)
- FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)

### 2. categories（分類表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| name       | varchar(50) | not null                | 分類名稱      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`id`)

### 3. tags（標籤表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| id         | bigint      | unsigned, auto_increment | 主鍵 ID      |
| name       | varchar(50) | not null                | 標籤名稱      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`id`)

### 4. article_tag（文章標籤關聯表）
| 欄位名稱    | 型別          | 屬性                      | 說明         |
|------------|--------------|--------------------------|-------------|
| article_id | bigint      | unsigned                | 文章 ID      |
| tag_id     | bigint      | unsigned                | 標籤 ID      |
| created_at | timestamp   | nullable                | 建立時間      |
| updated_at | timestamp   | nullable                | 更新時間      |

索引：
- PRIMARY KEY (`article_id`, `tag_id`)
- FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`)
- FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`)

## 資料表關聯說明

1. **文章與分類（一對多）**
   - 一個文章只能屬於一個分類
   - 一個分類可以有多個文章
   - 使用 `category_id` 作為外鍵

2. **文章與標籤（多對多）**
   - 一個文章可以有多個標籤
   - 一個標籤可以屬於多個文章
   - 使用 `article_tag` 關聯表建立多對多關係

## 注意事項

1. **軟刪除考量**
   - 如果之後需要軟刪除功能，可以在相關表加入 `deleted_at` 欄位

2. **索引優化**
   - 已加入基本的主鍵和外鍵索引
   - 可能需要根據實際查詢需求增加其他索引

3. **欄位長度**
   - 標題長度限制為 255 字元
   - 作者名稱限制為 100 字元
   - 分類和標籤名稱限制為 50 字元
   - 內容使用 text 型別，可存儲較長文字

4. **時間戳記**
   - 使用 Laravel 的 timestamps 功能
   - `articles`、`categories` 和 `tags` 表包含 `created_at` 和 `updated_at`
   - `article_tag` 關聯表不需要時間戳記 