<?php

namespace Tests\Feature\Api\Public\Tag;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 標籤詳情 API 測試
 * 
 * 測試範圍：
 * - GET /api/tags/{id}
 */
class TagDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試取得存在的標籤詳情
     */
    public function test_can_get_existing_tag_detail(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel'
        ]);

        // 建立一些文章來測試 articles_count
        $articles = Article::factory()->published()->count(2)->create([
            'category_id' => $category->id
        ]);
        
        foreach ($articles as $article) {
            $article->tags()->attach($tag->id);
        }

        // Act
        $response = $this->getJson("/api/tags/{$tag->id}");

        // Assert
        // Assert - 使用統一的成功斷言和資源結構驗證
        $this->assertApiSuccess($response, 200, '成功');
        $this->assertTagResourceStructure($response);
        $response
                 ->assertJsonPath('data.name', 'Laravel')
                 ->assertJsonPath('data.slug', 'laravel')
                 ->assertJsonPath('data.articles_count', 2);
    }

    /**
     * 測試取得不存在的標籤返回 404
     */
    public function test_get_nonexistent_tag_returns_404(): void
    {
        // Act
        $response = $this->getJson('/api/tags/999');

        // Assert
        $response->assertStatus(404);
    }

    /**
     * 測試標籤的文章計數正確性
     */
    public function test_tag_articles_count_accuracy(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        // 建立不同狀態的文章
        $publishedArticles = Article::factory()->published()->count(3)->create(['category_id' => $category->id]);
        $draftArticles = Article::factory()->draft()->count(2)->create(['category_id' => $category->id]);

        // 標籤關聯到所有文章
        foreach ($publishedArticles->merge($draftArticles) as $article) {
            $article->tags()->attach($tag->id);
        }

        // Act
        $response = $this->getJson("/api/tags/{$tag->id}");

        // Assert - 只計算已發布的文章
        // Assert - 使用統一的成功斷言
        $this->assertApiSuccess($response, 200, '成功');
        $response->assertJsonPath('data.articles_count', 3);
    }

    /**
     * 測試標籤沒有關聯文章時的計數
     */
    public function test_tag_with_no_articles(): void
    {
        // Arrange
        $tag = Tag::factory()->create();

        // Act
        $response = $this->getJson("/api/tags/{$tag->id}");

        // Assert
        // Assert - 使用統一的成功斷言
        $this->assertApiSuccess($response, 200, '成功');
        $response->assertJsonPath('data.articles_count', 0);
    }

    /**
     * 測試無效的標籤 ID 格式
     */
    public function test_invalid_tag_id_format(): void
    {
        // Act - 使用非數字 ID
        $response = $this->getJson('/api/tags/invalid');

        // Assert - 應該被路由約束阻擋，返回 404
        $response->assertStatus(404);
    }
}