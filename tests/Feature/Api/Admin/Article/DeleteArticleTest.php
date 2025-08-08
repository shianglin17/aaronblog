<?php

namespace Tests\Feature\Api\Admin\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * 文章刪除 API 測試
 * 
 * 測試範圍：
 * - DELETE /api/admin/articles/{id}
 * 
 * 確保 BaseRepository 的修改不影響 Article 模型的刪除操作
 */
class DeleteArticleTest extends AdminTestCase
{
    /**
     * 測試刪除文章
     */
    public function test_can_delete_article(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson("/api/admin/articles/{$article->id}");

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => '文章刪除成功'
                 ]);

        // 驗證文章已從資料庫中刪除
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id
        ]);
    }

    /**
     * 測試刪除不存在的文章
     */
    public function test_delete_non_existing_article_returns_404(): void
    {
        $response = $this->deleteJson('/api/admin/articles/999');

        $response->assertNotFound();
    }

    /**
     * 測試刪除有標籤關聯的文章
     */
    public function test_can_delete_article_with_tags(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);
        
        // 關聯標籤
        $article->tags()->attach([$tag1->id, $tag2->id]);

        $response = $this->deleteJson("/api/admin/articles/{$article->id}");

        $response->assertOk();

        // 驗證文章已刪除
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id
        ]);
        
        // 驗證標籤本身仍存在（多對多關聯的中間表記錄會自動清理）
        $this->assertDatabaseHas('tags', ['id' => $tag1->id]);
        $this->assertDatabaseHas('tags', ['id' => $tag2->id]);
    }

    /**
     * 測試刪除文章不會影響分類和標籤
     */
    public function test_delete_article_preserves_category_and_tags(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        
        // 創建兩篇文章，使用相同的分類和標籤
        $article1 = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);
        $article2 = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);
        
        $article1->tags()->attach([$tag->id]);
        $article2->tags()->attach([$tag->id]);

        // 刪除其中一篇文章
        $response = $this->deleteJson("/api/admin/articles/{$article1->id}");

        $response->assertOk();

        // 驗證分類和標籤仍然存在
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        
        // 驗證另一篇文章仍然存在且關聯正常
        $this->assertDatabaseHas('articles', ['id' => $article2->id]);
        $article2->refresh();
        $this->assertTrue($article2->tags->contains($tag->id), '剩餘文章的標籤關聯應該保持正常');
    }
}