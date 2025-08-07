<?php

namespace Tests\Feature\Api\Admin\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 文章快取失效測試
 * 
 * 測試文章 CRUD 操作是否正確清理相關快取：
 * - 文章快取
 * - 標籤快取（articles_count 變化）
 * - 分類快取（articles_count 變化）
 */
class ArticleCacheInvalidationTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試刪除文章時清理標籤快取
     */
    public function test_delete_article_clears_tag_cache(): void
    {
        // 建立測試資料
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $user = User::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);
        
        // 關聯標籤
        $article->tags()->attach([$tag1->id, $tag2->id]);

        // 第一次請求標籤列表（建立快取）
        $response1 = $this->getJson('/api/tags');
        $response1->assertOk();
        $tag1Count = collect($response1->json('data'))
            ->firstWhere('id', $tag1->id)['articles_count'];
        
        $this->assertEquals(1, $tag1Count, '標籤應該有1篇文章');

        // 刪除文章
        $response = $this->deleteJson("/api/admin/articles/{$article->id}");
        $response->assertOk();

        // 再次請求標籤列表（應該從更新後的快取獲取）
        $response2 = $this->getJson('/api/tags');
        $response2->assertOk();
        $tag1CountAfter = collect($response2->json('data'))
            ->firstWhere('id', $tag1->id)['articles_count'];
        
        $this->assertEquals(0, $tag1CountAfter, '刪除文章後標籤應該沒有文章');
    }

    /**
     * 測試刪除文章時清理分類快取
     */
    public function test_delete_article_clears_category_cache(): void
    {
        // 建立測試資料
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        // 第一次請求分類列表（建立快取）
        $response1 = $this->getJson('/api/categories');
        $response1->assertOk();
        $categoryCount = collect($response1->json('data'))
            ->firstWhere('id', $category->id)['articles_count'];
        
        $this->assertEquals(1, $categoryCount, '分類應該有1篇文章');

        // 刪除文章
        $response = $this->deleteJson("/api/admin/articles/{$article->id}");
        $response->assertOk();

        // 再次請求分類列表（應該從更新後的快取獲取）
        $response2 = $this->getJson('/api/categories');
        $response2->assertOk();
        $categoryCountAfter = collect($response2->json('data'))
            ->firstWhere('id', $category->id)['articles_count'];
        
        $this->assertEquals(0, $categoryCountAfter, '刪除文章後分類應該沒有文章');
    }

    /**
     * 測試創建文章時清理標籤快取
     */
    public function test_create_article_clears_tag_cache(): void
    {
        // 建立測試資料
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        
        // 第一次請求標籤列表（建立快取）
        $response1 = $this->getJson('/api/tags');
        $response1->assertOk();
        $tagCount = collect($response1->json('data'))
            ->firstWhere('id', $tag->id)['articles_count'];
        
        $this->assertEquals(0, $tagCount, '標籤應該沒有文章');

        // 創建文章並關聯標籤
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
        $response->assertStatus(201);

        // 再次請求標籤列表（應該從更新後的快取獲取）
        $response2 = $this->getJson('/api/tags');
        $response2->assertOk();
        $tagCountAfter = collect($response2->json('data'))
            ->firstWhere('id', $tag->id)['articles_count'];
        
        $this->assertEquals(1, $tagCountAfter, '創建文章後標籤應該有1篇文章');
    }

    /**
     * 測試更新文章標籤關聯時清理快取
     */
    public function test_update_article_tags_clears_cache(): void
    {
        // 建立測試資料
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $user = User::factory()->create();
        
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);
        
        // 初始關聯 tag1
        $article->tags()->attach([$tag1->id]);

        // 第一次請求標籤列表（建立快取）
        $response1 = $this->getJson('/api/tags');
        $response1->assertOk();
        $tag1Count = collect($response1->json('data'))
            ->firstWhere('id', $tag1->id)['articles_count'];
        $tag2Count = collect($response1->json('data'))
            ->firstWhere('id', $tag2->id)['articles_count'];
        
        $this->assertEquals(1, $tag1Count, 'tag1 應該有1篇文章');
        $this->assertEquals(0, $tag2Count, 'tag2 應該沒有文章');

        // 更新文章標籤關聯（改為 tag2）
        $updateData = [
            'title' => $article->title,
            'tags' => [$tag2->id]
        ];

        $response = $this->putJson("/api/admin/articles/{$article->id}", $updateData);
        $response->assertOk();

        // 再次請求標籤列表（應該從更新後的快取獲取）
        $response2 = $this->getJson('/api/tags');
        $response2->assertOk();
        $tag1CountAfter = collect($response2->json('data'))
            ->firstWhere('id', $tag1->id)['articles_count'];
        $tag2CountAfter = collect($response2->json('data'))
            ->firstWhere('id', $tag2->id)['articles_count'];
        
        $this->assertEquals(0, $tag1CountAfter, '更新後 tag1 應該沒有文章');
        $this->assertEquals(1, $tag2CountAfter, '更新後 tag2 應該有1篇文章');
    }
}