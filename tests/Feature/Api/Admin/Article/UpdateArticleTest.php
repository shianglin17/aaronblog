<?php

namespace Tests\Feature\Api\Admin\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 文章更新 API 測試
 * 
 * 測試範圍：
 * - PUT /api/admin/articles/{id}
 * - 文章所有權驗證
 * - AuthorizationException 處理
 * - 標籤關聯更新
 * 
 * 確保 BaseRepository 的修改不影響 Article 模型的更新操作
 */
class UpdateArticleTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試用戶可以更新自己的文章
     */
    public function test_user_can_update_own_article(): void
    {
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        
        // 建立屬於當前認證用戶的文章
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $this->authenticatedUser->id,
            'title' => '原始標題'
        ]);
        
        $article->tags()->attach([$tag1->id]);

        $updateData = [
            'title' => '更新的標題',
            'category_id' => $newCategory->id,
            'tags' => [$tag2->id]
        ];

        $response = $this->putJson("/api/admin/articles/{$article->id}", $updateData);

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => '成功'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'category' => ['id', 'name', 'slug'],
                         'updated_at'
                     ]
                 ]);

        // 驗證文章已更新
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => '更新的標題',
            'category_id' => $newCategory->id
        ]);
        
        // 驗證標籤關聯已更新
        $article->refresh();
        $this->assertTrue($article->tags->contains($tag2->id), '文章應該關聯到新標籤');
        $this->assertFalse($article->tags->contains($tag1->id), '文章不應該再關聯到舊標籤');
    }

    /**
     * 測試更新不存在的文章
     */
    public function test_update_non_existing_article_returns_404(): void
    {
        $response = $this->putJson('/api/admin/articles/999', [
            'title' => '測試標題'
        ]);

        $response->assertNotFound();
    }

    /**
     * 測試部分更新文章
     */
    public function test_can_partially_update_article(): void
    {
        $category = Category::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $this->authenticatedUser->id,
            'title' => '原始標題',
            'content' => '原始內容'
        ]);

        // 只更新標題
        $response = $this->putJson("/api/admin/articles/{$article->id}", [
            'title' => '新標題'
        ]);

        $response->assertOk();
        
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => '新標題',
            'content' => '原始內容' // 保持不變
        ]);
    }

    /**
     * 測試更新文章標籤關聯
     */
    public function test_can_update_article_tags(): void
    {
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $tag3 = Tag::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $this->authenticatedUser->id
        ]);
        
        // 初始關聯兩個標籤
        $article->tags()->attach([$tag1->id, $tag2->id]);

        // 更新為不同的標籤組合
        $updateData = [
            'tags' => [$tag2->id, $tag3->id] // 保留 tag2，移除 tag1，添加 tag3
        ];

        $response = $this->putJson("/api/admin/articles/{$article->id}", $updateData);

        $response->assertOk();
        
        // 驗證標籤關聯已正確更新
        $article->refresh();
        $this->assertCount(2, $article->tags, '文章應該有2個標籤');
        $this->assertFalse($article->tags->contains($tag1->id), '不應該再關聯 tag1');
        $this->assertTrue($article->tags->contains($tag2->id), '應該保留 tag2');
        $this->assertTrue($article->tags->contains($tag3->id), '應該新增 tag3');
    }
}