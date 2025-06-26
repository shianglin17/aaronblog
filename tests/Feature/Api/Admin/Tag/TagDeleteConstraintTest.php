<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class TagDeleteConstraintTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試刪除沒有文章使用的標籤（應該成功）
     */
    public function test_can_delete_tag_when_no_articles_using_it(): void
    {
        // 建立一個沒有文章的標籤
        $tag = Tag::factory()->create();

        // 嘗試刪除標籤
        $response = $this->deleteJson("/api/admin/tags/{$tag->id}");

        // 應該成功刪除
        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => '標籤刪除成功'
        ]);

        // 確認標籤已被刪除
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /**
     * 測試刪除有文章使用的標籤（應該失敗）
     */
    public function test_cannot_delete_tag_when_articles_using_it(): void
    {
        // 建立標籤、分類、用戶和文章
        $tag = Tag::factory()->create();
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        $articles = Article::factory()->count(2)->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        // 將標籤關聯到文章
        foreach ($articles as $article) {
            $article->tags()->attach($tag->id);
        }

        // 嘗試刪除標籤
        $response = $this->deleteJson("/api/admin/tags/{$tag->id}");

        // 應該返回錯誤
        $response->assertStatus(409);
        $response->assertJson([
            'status' => 'error',
            'message' => "無法刪除 標籤（ID: {$tag->id}），因為仍有 2 篇文章正在使用此標籤"
        ]);

        // 確認標籤仍然存在
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    /**
     * 測試刪除不存在的標籤
     */
    public function test_delete_non_existing_tag_returns_404(): void
    {
        $response = $this->deleteJson('/api/admin/tags/999');

        $response->assertNotFound();
    }
} 