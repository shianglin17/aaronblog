<?php

namespace Tests\Feature\Api\Public\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * 公開文章 API 測試
 * 
 * 測試範圍：
 * - GET /api/articles (列表)
 * - GET /api/articles/{id} (詳情)
 * - 分頁、搜尋、排序、過濾功能
 */
class ArticleApiTest extends TestCase
{
    use RefreshDatabase;
    
    protected Category $defaultCategory;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // 只創建基礎的預設分類，避免資料庫唯一約束衝突
        $this->defaultCategory = Category::factory()->create([
            'name' => 'Default Category',
            'slug' => 'default-category'
        ]);
    }

    /**
     * 測試取得文章列表 - 基本格式
     */
    public function test_get_articles_list_returns_correct_format(): void
    {
        // Arrange: 建立測試資料
        Article::factory()->published()->count(3)->create([
            'category_id' => $this->defaultCategory->id
        ]);

        // Act: 呼叫 API
        $response = $this->getJson('/api/articles');

        // Assert: 驗證回應格式
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'code', 
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'slug',
                             'description',
                             'content',
                             'status',
                             'created_at',
                             'updated_at',
                             'author' => [
                                 'id',
                                 'name'
                             ],
                             'category' => [
                                 'id',
                                 'name',
                                 'slug'
                             ],
                             'tags' => [
                                 '*' => [
                                     'id',
                                     'name',
                                     'slug'
                                 ]
                             ]
                         ]
                     ],
                     'meta' => [
                         'pagination' => [
                             'current_page',
                             'total_pages',
                             'total_items',
                             'per_page'
                         ]
                     ]
                 ])
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('code', 200)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * 測試分頁功能的各種參數組合
     */
    #[DataProvider('paginationProvider')]
    public function test_pagination_with_various_parameters(
        int $totalArticles,
        int $perPage, 
        int $page,
        int $expectedCount,
        int $expectedTotalPages
    ): void {
        // Arrange: 建立指定數量的文章
        Article::factory()->published()->count($totalArticles)->create([
            'category_id' => $this->defaultCategory->id
        ]);

        // Act: 呼叫 API
        $response = $this->getJson("/api/articles?per_page={$perPage}&page={$page}");

        // Assert: 驗證分頁結果
        $response->assertStatus(200)
                 ->assertJsonCount($expectedCount, 'data')
                 ->assertJsonPath('meta.pagination.per_page', $perPage)
                 ->assertJsonPath('meta.pagination.current_page', $page)
                 ->assertJsonPath('meta.pagination.total_pages', $expectedTotalPages)
                 ->assertJsonPath('meta.pagination.total_items', $totalArticles);
    }

    /**
     * 分頁測試資料提供者
     */
    public static function paginationProvider(): array
    {
        return [
            // [總文章數, 每頁筆數, 頁碼, 預期資料筆數, 預期總頁數]
            'first page with 10 per page' => [25, 10, 1, 10, 3],
            'second page with 10 per page' => [25, 10, 2, 10, 3],
            'last page with 10 per page' => [25, 10, 3, 5, 3],
            'first page with 5 per page' => [12, 5, 1, 5, 3],
            'middle page with 3 per page' => [10, 3, 2, 3, 4],
            'last page with 3 per page' => [10, 3, 4, 1, 4],
            'single page' => [3, 10, 1, 3, 1],
            'empty result page' => [5, 3, 3, 0, 2], // 超出範圍的頁碼
        ];
    }

    /**
     * 測試搜尋功能的各種關鍵字
     */
    #[DataProvider('searchProvider')]
    public function test_search_with_various_keywords(
        array $articleTitles,
        string $searchKeyword,
        array $expectedTitles
    ): void {
        // Arrange: 建立指定標題的文章
        foreach ($articleTitles as $title) {
            Article::factory()->published()->create([
                'title' => $title,
                'category_id' => $this->defaultCategory->id
            ]);
        }

        // Act: 搜尋
        $response = $this->getJson("/api/articles?search=" . urlencode($searchKeyword));

        // Assert: 驗證搜尋結果
        $response->assertStatus(200)
                 ->assertJsonCount(count($expectedTitles), 'data');

        if (!empty($expectedTitles)) {
            $responseData = $response->json('data');
            $actualTitles = collect($responseData)->pluck('title')->toArray();
            
            foreach ($expectedTitles as $expectedTitle) {
                $this->assertContains($expectedTitle, $actualTitles);
            }
        }
    }

    /**
     * 搜尋測試資料提供者
     */
    public static function searchProvider(): array
    {
        return [
            'search Laravel' => [
                ['Laravel 最佳實踐', 'Vue.js 入門', 'Laravel 進階技巧', 'React 基礎'],
                'Laravel',
                ['Laravel 最佳實踐', 'Laravel 進階技巧']
            ],
            'search Vue' => [
                ['Laravel 最佳實踐', 'Vue.js 入門', 'Vue 3 新特性', 'React 基礎'],
                'Vue',
                ['Vue.js 入門', 'Vue 3 新特性']
            ],
            'case insensitive search' => [
                ['LARAVEL Tutorial', 'laravel guide', 'Vue Tutorial'],
                'laravel',
                ['LARAVEL Tutorial', 'laravel guide']
            ],
            'no results' => [
                ['Laravel Tutorial', 'Vue Guide'],
                'Django',
                []
            ],
            'empty search' => [
                ['Laravel Tutorial', 'Vue Guide'],
                '',
                ['Laravel Tutorial', 'Vue Guide'] // 空搜尋應回傳所有文章
            ]
        ];
    }

    /**
     * 測試排序功能的各種組合
     */
    #[DataProvider('sortingProvider')]
    public function test_sorting_with_various_parameters(
        array $articleData,
        string $sortBy,
        string $sortDirection,
        array $expectedOrder
    ): void {
        // Arrange: 建立指定的文章
        foreach ($articleData as $data) {
            Article::factory()->published()->create(array_merge($data, [
                'category_id' => $this->defaultCategory->id
            ]));
        }

        // Act: 排序查詢
        $response = $this->getJson("/api/articles?sort_by={$sortBy}&sort_direction={$sortDirection}");

        // Assert: 驗證排序結果
        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $actualTitles = collect($responseData)->pluck('title')->toArray();

        $this->assertEquals($expectedOrder, $actualTitles);
    }

    /**
     * 排序測試資料提供者
     */
    public static function sortingProvider(): array
    {
        return [
            'title ascending' => [
                [
                    ['title' => 'C Article', 'created_at' => now()->subDays(1)],
                    ['title' => 'A Article', 'created_at' => now()->subDays(2)],
                    ['title' => 'B Article', 'created_at' => now()->subDays(3)],
                ],
                'title',
                'asc',
                ['A Article', 'B Article', 'C Article']
            ],
            'title descending' => [
                [
                    ['title' => 'A Article', 'created_at' => now()->subDays(1)],
                    ['title' => 'C Article', 'created_at' => now()->subDays(2)],
                    ['title' => 'B Article', 'created_at' => now()->subDays(3)],
                ],
                'title',
                'desc',
                ['C Article', 'B Article', 'A Article']
            ],
            'created_at descending (newest first)' => [
                [
                    ['title' => 'Old Article', 'created_at' => now()->subDays(5)],
                    ['title' => 'New Article', 'created_at' => now()->subDays(1)],
                    ['title' => 'Middle Article', 'created_at' => now()->subDays(3)],
                ],
                'created_at',
                'desc',
                ['New Article', 'Middle Article', 'Old Article']
            ],
            'created_at ascending (oldest first)' => [
                [
                    ['title' => 'New Article', 'created_at' => now()->subDays(1)],
                    ['title' => 'Old Article', 'created_at' => now()->subDays(5)],
                    ['title' => 'Middle Article', 'created_at' => now()->subDays(3)],
                ],
                'created_at',
                'asc',
                ['Old Article', 'Middle Article', 'New Article']
            ]
        ];
    }

    /**
     * 測試文章分類過濾功能
     */
    public function test_articles_category_filtering(): void
    {
        // Arrange: 建立不同分類的文章
        $techCategory = Category::factory()->create([
            'name' => 'Technology',
            'slug' => 'technology'
        ]);
        $lifestyleCategory = Category::factory()->create([
            'name' => 'Lifestyle', 
            'slug' => 'lifestyle'
        ]);
        
        Article::factory()->published()->create([
            'title' => 'Tech Article',
            'category_id' => $techCategory->id
        ]);
        
        Article::factory()->published()->create([
            'title' => 'Life Article', 
            'category_id' => $lifestyleCategory->id
        ]);

        // Act & Assert: 按分類 slug 過濾
        $response = $this->getJson('/api/articles?category=lifestyle');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Life Article')
                 ->assertJsonPath('data.0.category.name', 'Lifestyle');
    }

    /**
     * 測試文章標籤過濾功能
     */
    public function test_articles_tags_filtering(): void
    {
        // Arrange: 建立不同標籤的文章
        $phpTag = Tag::factory()->create([
            'name' => 'PHP',
            'slug' => 'php'
        ]);
        $laravelTag = Tag::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel'
        ]);
        $jsTag = Tag::factory()->create([
            'name' => 'JavaScript',
            'slug' => 'javascript'
        ]);

        $phpArticle = Article::factory()->published()->create([
            'title' => 'PHP Article',
            'category_id' => $this->defaultCategory->id
        ]);
        $phpArticle->tags()->attach([$phpTag->id]);

        $fullStackArticle = Article::factory()->published()->create([
            'title' => 'Full Stack Article',
            'category_id' => $this->defaultCategory->id
        ]);
        $fullStackArticle->tags()->attach([$phpTag->id, $laravelTag->id]);

        $jsArticle = Article::factory()->published()->create([
            'title' => 'JS Article',
            'category_id' => $this->defaultCategory->id
        ]);
        $jsArticle->tags()->attach([$jsTag->id]);

        // Act & Assert: 按單一標籤過濾
        $response = $this->getJson('/api/articles?tags[]=php');
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // PHP Article + Full Stack Article

        // Act & Assert: 按多個標籤過濾
        $response = $this->getJson('/api/articles?tags[]=php&tags[]=laravel');
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // 應該還是 2 個（有 php 或 laravel 的文章）
    }

    /**
     * 測試只顯示已發布的文章
     */
    public function test_public_route_only_shows_published_articles(): void
    {
        // Arrange: 建立不同狀態的文章
        Article::factory()->published()->count(2)->create([
            'category_id' => $this->defaultCategory->id
        ]);
        Article::factory()->draft()->count(3)->create([
            'category_id' => $this->defaultCategory->id
        ]);

        // Act: 呼叫公開 API
        $response = $this->getJson('/api/articles');

        // Assert: 只顯示已發布的文章
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');

        // 驗證所有回傳的文章都是 published 狀態
        $articles = $response->json('data');
        foreach ($articles as $article) {
            $this->assertEquals('published', $article['status']);
        }
    }

    /**
     * 測試取得單篇文章詳情
     */
    public function test_get_article_by_id_returns_correct_format(): void
    {
        // Arrange: 建立測試文章
        $article = Article::factory()->published()->create([
            'title' => 'Test Article',
            'category_id' => $this->defaultCategory->id
        ]);

        // Act: 呼叫 API
        $response = $this->getJson("/api/articles/{$article->id}");

        // Assert: 驗證回應格式
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message', 
                     'data' => [
                         'id',
                         'title',
                         'slug',
                         'description',
                         'content',
                         'status',
                         'created_at',
                         'updated_at',
                         'author' => [
                             'id',
                             'name'
                         ],
                         'category' => [
                             'id',
                             'name',
                             'slug'
                         ],
                         'tags' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'slug'
                             ]
                         ]
                     ]
                 ])
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('code', 200)
                 ->assertJsonPath('data.title', 'Test Article')
                 ->assertJsonPath('data.id', $article->id);
    }

    /**
     * 測試取得不存在的文章回傳 404
     */
    public function test_get_nonexistent_article_returns_404(): void
    {
        $response = $this->getJson('/api/articles/999');
        
        $response->assertStatus(404);
        // Laravel 預設的 ModelNotFoundException 處理會回傳 HTML 頁面
        // 如果需要 JSON 格式，需要自定義異常處理
    }

    /**
     * 測試搜尋時不會出現草稿文章
     */
    public function test_search_does_not_include_draft_articles(): void
    {
        // Arrange: 建立包含相同關鍵字的已發布和草稿文章
        Article::factory()->published()->create([
            'title' => 'Laravel 最佳實踐',
            'category_id' => $this->defaultCategory->id
        ]);
        Article::factory()->draft()->create([
            'title' => 'Laravel 進階技巧',
            'category_id' => $this->defaultCategory->id
        ]);
        Article::factory()->published()->create([
            'title' => 'Vue.js 入門',
            'category_id' => $this->defaultCategory->id
        ]);
        Article::factory()->draft()->create([
            'title' => 'Laravel 開發指南',
            'category_id' => $this->defaultCategory->id
        ]);

        // Act: 搜尋包含 "Laravel" 的文章
        $response = $this->getJson('/api/articles?search=Laravel');

        // Assert: 驗證只回傳已發布的文章
        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $this->assertCount(1, $responseData); // 只應該有 1 篇已發布的 Laravel 文章
        
        $this->assertEquals('Laravel 最佳實踐', $responseData[0]['title']);
        $this->assertEquals('published', $responseData[0]['status']);
    }
} 