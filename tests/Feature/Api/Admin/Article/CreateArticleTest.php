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

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => '文章創建成功'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'slug',
                         'description',
                         'content',
                         'status',
                         'author' => ['id', 'name'],
                         'category' => ['id', 'name', 'slug'],
                         'tags',
                         'created_at'
                     ]
                 ]);

        // 驗證文章存在於資料庫
        $this->assertDatabaseHas('articles', [
            'title' => '測試文章',
            'slug' => 'test-article',
            'category_id' => $category->id
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

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'meta' => [
                         'errors'
                     ]
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

        $response->assertStatus(201);
        
        // 驗證所有標籤都已關聯
        $article = Article::where('slug', 'multi-tag-article')->first();
        $this->assertCount(3, $article->tags, '文章應該關聯到3個標籤');
        $this->assertTrue($article->tags->contains($tag1->id));
        $this->assertTrue($article->tags->contains($tag2->id));
        $this->assertTrue($article->tags->contains($tag3->id));
    }
}