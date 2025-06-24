# 測試架構

## 目標
在重構前建立基本測試，確保不破壞現有功能。

## 測試策略

### 測試金字塔
```
    🔺 UI Tests (未實作)
   ────────────────────
  🔺🔺 Integration Tests  
 ──────────────────────────
🔺🔺🔺 Unit Tests (最小化)
────────────────────────────
```

**設計原則：**
- **Integration Tests 為主**：測試完整的 API 流程
- **Unit Tests 最小化**：只在必要時測試複雜業務邏輯
- **快速執行**：整個測試套件 < 10 秒

### 測試環境
- **資料庫**: 內存 SQLite (`:memory:`)
- **快取**: Array 驅動
- **執行時間**: 每個測試 < 1 秒

## 文件結構

```
tests/
├── Feature/                           # Integration Tests
│   └── Api/                          # API 相關測試
│       ├── Public/                   # 公開 API（無需認證）
│       │   ├── ArticleApiTest.php    # 文章 API 測試
│       │   ├── CategoryApiTest.php   # 分類 API 測試
│       │   ├── TagApiTest.php        # 標籤 API 測試
│       │   └── GeneralApiTest.php    # 通用功能測試
│       └── Auth/                     # 認證 API 測試（未來）
└── Unit/                             # Unit Tests（最小化）
    ├── Services/                     # 業務邏輯測試
    └── Repositories/                 # 資料存取測試
```

## 測試分類說明

### API Integration Tests (`Feature/Api/`)

**目的：** 測試完整的 API 端到端功能

**特點：**
- 發送真實 HTTP 請求
- 使用真實資料庫（SQLite in-memory）
- 測試路由、中介軟體、控制器、服務、資料庫
- 驗證 JSON 回應格式和業務邏輯

**範例：**
```php
public function test_get_articles_with_pagination(): void
{
    // Arrange: 建立測試資料
    Article::factory()->published()->count(15)->create();
    
    // Act: 發送 API 請求
    $response = $this->getJson('/api/articles?page=1&per_page=10');
    
    // Assert: 驗證完整回應
    $response->assertStatus(200)
             ->assertJsonStructure(['data', 'meta'])
             ->assertJsonPath('meta.total', 15)
             ->assertJsonPath('meta.per_page', 10);
}
```

### Unit Tests (`Unit/`)

**最小化原則：** 只在以下情況下建立 Unit Tests：
- 複雜的業務邏輯計算
- 複雜的資料處理邏輯
- 需要測試多種邊界條件的方法

## 測試基礎設施

### 測試基礎類別
```php
abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // 清除快取避免測試間干擾
        Cache::flush();
        
        // 設定測試環境
        config(['app.env' => 'testing']);
    }
}
```

### 資料工廠使用
```php
// 使用工廠建立測試資料
Article::factory()
    ->published()
    ->withTags(['Laravel', 'PHP'])
    ->count(10)
    ->create();
```

## 測試覆蓋率目標

| 測試類型 | 覆蓋率目標 | 優先級 |
|---------|-----------|--------|
| API Integration | 100% | 最高 |
| Unit Tests | 90% | 低 |

**重點：** 確保所有 API 端點都有完整的測試覆蓋

## 測試原則
1. **最小可行測試** - 只測試核心功能
2. **快速執行** - 整個測試套件 < 10 秒
3. **獨立性** - 每個測試互不影響
4. **真實場景** - 使用真實資料和邊界條件
5. **AAA 模式** - Arrange, Act, Assert 

---

## 📚 **參考資源**

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Test-Driven Development](https://martinfowler.com/bliki/TestDrivenDevelopment.html)
- [Testing Pyramid](https://martinfowler.com/articles/practical-test-pyramid.html) 
