<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\Tag;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * 標籤已發布文章計數測試
 * 
 * 測試 articles_count 只包含已發布的文章
 */
class TagPublishedArticlesCountTest extends AdminTestCase
{
    /**
     * 測試標籤計數只包含已發布文章
     */
    public function test_tag_articles_count_only_includes_published_articles(): void
    {
        // 建立測試資料
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        
        // 創建 3 篇已發布文章
        $publishedArticles = Article::factory()->count(3)->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'published'
        ]);
        
        // 創建 2 篇草稿文章
        $draftArticles = Article::factory()->count(2)->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'draft'
        ]);
        
        // 將所有文章都關聯到標籤
        $allArticles = $publishedArticles->merge($draftArticles);
        $tag->articles()->attach($allArticles->pluck('id')->toArray());

        // 獲取標籤列表
        $response = $this->getJson('/api/tags');

        $response->assertOk();
        
        $tagData = collect($response->json('data'))
            ->firstWhere('id', $tag->id);
            
        // 驗證只計算已發布文章
        $this->assertEquals(3, $tagData['articles_count'], '標籤應該只計算已發布文章的數量');
    }

    /**
     * 測試更新標籤後文章計數正確
     */
    public function test_update_tag_articles_count_only_includes_published(): void
    {
        // 建立測試資料
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '原始標籤']);
        
        // 創建 1 篇已發布文章和 1 篇草稿文章
        $publishedArticle = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'published'
        ]);
        
        $draftArticle = Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'draft'
        ]);
        
        $tag->articles()->attach([$publishedArticle->id, $draftArticle->id]);

        // 更新標籤
        $response = $this->putJson("/api/admin/tags/{$tag->id}", [
            'name' => '更新的標籤名稱'
        ]);

        $response->assertOk();
        
        $responseData = $response->json('data');
        $this->assertEquals(1, $responseData['articles_count'], '更新標籤時應該只計算已發布文章');
    }

    /**
     * 測試創建標籤時的文章計數
     */
    public function test_create_tag_articles_count_is_zero(): void
    {
        $response = $this->postJson('/api/admin/tags', [
            'name' => '新標籤',
            'slug' => 'new-tag'
        ]);

        $response->assertStatus(201);
        
        $responseData = $response->json('data');
        $this->assertEquals(0, $responseData['articles_count'], '新創建的標籤文章計數應該是0');
    }
}