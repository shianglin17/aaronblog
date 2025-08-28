<?php

namespace Tests\Feature\Api\Admin\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 文章創建 API 測試
 * 
 * 測試範圍：
 * - POST /api/admin/articles
 * - 自動設定 user_id
 * - AuthenticatedUser Trait 功能
 * 
 * 確保 BaseRepository 的修改不影響 Article 模型的創建操作
 */
class CreateArticleTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試創建文章
     */
    public function test_can_create_article(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        
        $articleData = [
            'title' => '測試文章',
            'slug' => 'test-article',
            'description' => '測試描述',
            'content' => '測試內容',
            'status' => 'published',
            'category_id' => $category->id,
            'tags' => [$tag->id]
        ];

        $response = $this->postJson('/api/admin/articles', $articleData);

        // Assert - 使用統一的斷言方法
        $this->assertApiSuccess($response, 201, '創建成功');
        $this->assertArticleResourceStructure($response);

        // 驗證文章存在於資料庫，且自動設定為當前用戶
        $this->assertDatabaseHas('articles', [
            'title' => '測試文章',
            'slug' => 'test-article',
            'category_id' => $category->id,
            'user_id' => $this->authenticatedUser->id  // 驗證自動設定user_id
        ]);
        
        // 驗證標籤關聯
        $article = Article::where('slug', 'test-article')->first();
        $this->assertTrue($article->tags->contains($tag->id), '文章應該關聯到指定的標籤');
    }

    /**
     * 測試創建文章時的驗證
     */
    public function test_create_article_validation(): void
    {
        $response = $this->postJson('/api/admin/articles', []);

        // Assert - 使用統一的錯誤斷言
        $this->assertApiError($response, 422);
    }

    /**
     * 測試文章自動關聯到當前認證用戶
     */
    public function test_article_automatically_assigned_to_current_user(): void
    {
        $category = Category::factory()->create();
        
        $articleData = [
            'title' => '用戶文章',
            'slug' => 'user-article',
            'description' => '測試描述',
            'content' => '測試內容',
            'status' => 'published',
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/admin/articles', $articleData);

        // Assert - 使用統一的斷言且驗證作者資訊
        $this->assertApiSuccess($response, 201, '創建成功');
        $response->assertJsonPath('data.author.id', $this->authenticatedUser->id);
                 
        // 驗證資料庫中的文章歸屬
        $this->assertDatabaseHas('articles', [
            'slug' => 'user-article',
            'user_id' => $this->authenticatedUser->id
        ]);
    }

    /**
     * 測試創建文章並關聯多個標籤
     */
    public function test_can_create_article_with_multiple_tags(): void
    {
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $tag3 = Tag::factory()->create();
        
        $articleData = [
            'title' => '多標籤文章',
            'slug' => 'multi-tag-article',
            'description' => '測試描述',
            'content' => '測試內容',
            'status' => 'published',
            'category_id' => $category->id,
            'tags' => [$tag1->id, $tag2->id, $tag3->id]
        ];

        $response = $this->postJson('/api/admin/articles', $articleData);

        // Assert - 使用統一的成功斷言
        $this->assertApiSuccess($response, 201, '創建成功');
        
        // 驗證所有標籤都已關聯且屬於當前用戶
        $article = Article::where('slug', 'multi-tag-article')->first();
        $this->assertEquals($this->authenticatedUser->id, $article->user_id);
        $this->assertCount(3, $article->tags, '文章應該關聯到3個標籤');
        $this->assertTrue($article->tags->contains($tag1->id));
        $this->assertTrue($article->tags->contains($tag2->id));
        $this->assertTrue($article->tags->contains($tag3->id));
    }
}