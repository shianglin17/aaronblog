<?php

namespace Tests\Feature\Api\Public\Category;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 分類詳情 API 測試
 * 
 * 測試範圍：
 * - GET /api/categories/{id}
 */
class CategoryDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試取得存在的分類詳情
     */
    public function test_can_get_existing_category_detail(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Tech articles'
        ]);

        // 建立一些文章來測試 articles_count
        Article::factory()->published()->count(3)->create([
            'category_id' => $category->id
        ]);

        // Act
        $response = $this->getJson("/api/categories/{$category->id}");

        // Assert - 使用統一的成功斷言和資源結構驗證
        $this->assertApiSuccess($response, 200, '成功');
        $this->assertCategoryResourceStructure($response);
        $response
                 ->assertJsonPath('data.name', 'Technology')
                 ->assertJsonPath('data.slug', 'technology')
                 ->assertJsonPath('data.description', 'Tech articles')
                 ->assertJsonPath('data.articles_count', 3);
    }

    /**
     * 測試取得不存在的分類返回 404
     */
    public function test_get_nonexistent_category_returns_404(): void
    {
        // Act
        $response = $this->getJson('/api/categories/999');

        // Assert
        $response->assertStatus(404);
    }

    /**
     * 測試分類的文章計數正確性
     */
    public function test_category_articles_count_accuracy(): void
    {
        // Arrange
        $category = Category::factory()->create();

        // 建立不同狀態的文章
        Article::factory()->published()->count(2)->create(['category_id' => $category->id]);
        Article::factory()->draft()->count(1)->create(['category_id' => $category->id]);

        // Act
        $response = $this->getJson("/api/categories/{$category->id}");

        // Assert - 只計算已發布的文章
        // Assert - 使用統一的成功斷言
        $this->assertApiSuccess($response, 200, '成功');
        $response->assertJsonPath('data.articles_count', 2);
    }

    /**
     * 測試無效的分類 ID 格式
     */
    public function test_invalid_category_id_format(): void
    {
        // Act - 使用非數字 ID
        $response = $this->getJson('/api/categories/invalid');

        // Assert - 應該被路由約束阻擋，返回 404
        $response->assertStatus(404);
    }
}