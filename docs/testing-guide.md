# 測試指南

## 🚀 **快速開始**

### 執行測試
```bash
# 執行所有測試
php artisan test

# 執行 API 測試
php artisan test tests/Feature/Api/Public/

# 執行特定測試檔案
php artisan test tests/Feature/Api/Public/ArticleApiTest.php

# 執行特定測試方法
php artisan test --filter=test_get_articles_with_pagination

# 顯示詳細錯誤訊息
php artisan test --verbose

# 測試失敗時停止
php artisan test --stop-on-failure
```

### 測試環境設定
測試會自動使用：
- **資料庫**：內存 SQLite (`:memory:`)
- **快取**：Array 驅動
- **每次測試後自動清理**

---

## 📊 **當前測試狀態**

### 測試覆蓋情況
| API 端點 | 測試檔案 | 測試數量 | 狀態 |
|---------|---------|---------|------|
| `GET /api/articles` | `ArticleApiTest.php` | 22 | ✅ 完成 |
| `GET /api/articles/{id}` | `ArticleApiTest.php` | 2 | ✅ 完成 |
| `GET /api/categories` | `CategoryApiTest.php` | 2 | ✅ 完成 |
| `GET /api/tags` | `TagApiTest.php` | 2 | ✅ 完成 |
| `GET /api/` | `GeneralApiTest.php` | 4 | ✅ 完成 |

**總計：32 個測試，全部通過 ✅**

### 測試功能覆蓋
- ✅ **分頁功能**：8 種分頁情況
- ✅ **搜尋功能**：5 種搜尋情況
- ✅ **排序功能**：4 種排序情況
- ✅ **過濾功能**：分類和標籤過濾
- ✅ **權限控制**：只顯示已發布文章
- ✅ **錯誤處理**：404 錯誤
- ✅ **資料隔離**：測試間互不干擾

---

## ✍️ **撰寫測試**

### 基本結構
```php
<?php

namespace Tests\Feature\Api\Public;

use App\Models\Article;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_articles_returns_success(): void
    {
        // Arrange: 準備測試資料
        Article::factory()->published()->count(3)->create();

        // Act: 發送請求
        $response = $this->getJson('/api/articles');

        // Assert: 驗證結果
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'content']
                     ]
                 ]);
    }
}
```

### 測試方法命名
- `test_` 開頭
- 描述功能和條件
- 例如：`test_get_articles_with_pagination`

---

## 🔧 **常用斷言**

### HTTP 回應
```php
$response->assertStatus(200);              // 狀態碼
$response->assertJson(['success' => true]); // JSON 內容
$response->assertJsonPath('data.0.id', 1); // 特定路徑
$response->assertJsonCount(3, 'data');      // 陣列長度
$response->assertJsonStructure([           // JSON 結構
    'data' => ['*' => ['id', 'title']]
]);
```

### 資料庫
```php
$this->assertDatabaseHas('articles', ['title' => 'Test']);
$this->assertDatabaseCount('articles', 3);
```

---

## 🛠️ **測試資料**

### 使用 Factory
```php
// 建立單筆資料
$article = Article::factory()->create();

// 建立多筆資料
Article::factory()->count(5)->create();

// 建立特定狀態資料
Article::factory()->published()->create();

// 建立關聯資料
Article::factory()
    ->published()
    ->withTags(['Laravel', 'PHP'])
    ->count(10)
    ->create();
```

### 自訂資料
```php
$article = Article::factory()->create([
    'title' => '特定標題',
    'status' => 'published'
]);
```

---

## 🐛 **常見問題排除**

### 測試失敗：資料庫連線錯誤
```bash
# 檢查 SQLite 設定
php artisan config:show database.connections.sqlite

# 確保測試資料庫檔案存在
touch database/database.sqlite
```

### 測試失敗：快取干擾
```bash
# 手動清除快取
php artisan cache:clear
php artisan config:clear

# 重新執行測試
php artisan test
```

### 測試執行緩慢
```bash
# 使用平行執行（需要安裝 paratest）
composer require --dev brianium/paratest
php artisan test --parallel
```

---

## 🎯 **開發工作流程**

### 新功能開發（TDD）
```bash
# 1. 先寫測試
nano tests/Feature/Api/Public/NewFeatureTest.php

# 2. 執行測試（應該失敗）
php artisan test tests/Feature/Api/Public/NewFeatureTest.php

# 3. 實作功能
nano app/Http/Controllers/NewFeatureController.php

# 4. 再次執行測試（應該通過）
php artisan test tests/Feature/Api/Public/NewFeatureTest.php

# 5. 執行所有測試確保沒有破壞現有功能
php artisan test
```

### 重構程式碼
```bash
# 1. 重構前執行所有測試
php artisan test

# 2. 進行重構
nano app/Services/ArticleService.php

# 3. 重構後執行所有測試
php artisan test

# 4. 確保所有測試都通過
```

---

## 📋 **檢查清單**

### 測試撰寫時確認
- [ ] 測試方法名稱清楚描述功能
- [ ] 使用 AAA 模式：Arrange, Act, Assert
- [ ] 測試獨立運行（不依賴其他測試）
- [ ] 驗證重要的業務邏輯
- [ ] 測試邊界條件和錯誤情況

### 提交程式碼前
- [ ] 執行完整測試套件：`php artisan test`
- [ ] 確保所有測試通過
- [ ] 檢查測試覆蓋率
- [ ] 確認沒有破壞現有功能 