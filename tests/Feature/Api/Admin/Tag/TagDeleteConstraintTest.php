<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 標籤刪除約束測試
 * 
 * 測試標籤刪除的寬鬆模式：
 * - 允許刪除有關聯文章的標籤
 * - 自動清理多對多關聯記錄
 * - 保持文章和其他標籤的完整性
 */
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
     * 測試刪除有文章使用的標籤（寬鬆模式：應該成功）
     */
    public function test_can_delete_tag_when_articles_using_it_loose_mode(): void
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

        // 確認關聯已建立
        $this->assertDatabaseHas('article_tag', ['tag_id' => $tag->id]);

        // 嘗試刪除標籤（寬鬆模式：允許刪除）
        $response = $this->deleteJson("/api/admin/tags/{$tag->id}");

        // 應該成功刪除
        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => '標籤刪除成功'
        ]);

        // 確認標籤已被刪除
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        
        // 確認中間表關聯也被自動清理
        $this->assertDatabaseMissing('article_tag', ['tag_id' => $tag->id]);
        
        // 確認文章仍然存在（只是失去了標籤關聯）
        foreach ($articles as $article) {
            $this->assertDatabaseHas('articles', ['id' => $article->id]);
        }
    }

    /**
     * 測試刪除不存在的標籤
     */
    public function test_delete_non_existing_tag_returns_404(): void
    {
        $response = $this->deleteJson('/api/admin/tags/999');

        $response->assertNotFound();
    }

    /**
     * 測試刪除標籤時多對多關聯的自動清理
     */
    public function test_tag_deletion_automatically_cleans_many_to_many_relationships(): void
    {
        // 建立多個標籤、分類、用戶和文章
        $targetTag = Tag::factory()->create(['name' => 'Target Tag']);
        $otherTag = Tag::factory()->create(['name' => 'Other Tag']);
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        $articles = Article::factory()->count(3)->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        // 建立複雜的標籤關聯
        foreach ($articles as $index => $article) {
            $article->tags()->attach($targetTag->id);
            if ($index < 2) {
                $article->tags()->attach($otherTag->id); // 前兩篇文章也有其他標籤
            }
        }

        // 確認關聯狀態
        $this->assertEquals(3, $targetTag->articles()->count());
        $this->assertEquals(2, $otherTag->articles()->count());
        $this->assertDatabaseCount('article_tag', 5); // 3 + 2 = 5 條關聯記錄

        // 刪除目標標籤
        $response = $this->deleteJson("/api/admin/tags/{$targetTag->id}");

        // 應該成功刪除
        $response->assertOk();

        // 確認目標標籤已被刪除
        $this->assertDatabaseMissing('tags', ['id' => $targetTag->id]);
        
        // 確認只有目標標籤的關聯被清理，其他標籤關聯保留
        $this->assertDatabaseMissing('article_tag', ['tag_id' => $targetTag->id]);
        $this->assertDatabaseCount('article_tag', 2); // 剩下其他標籤的 2 條關聯
        
        // 確認其他標籤和文章都還存在
        $this->assertDatabaseHas('tags', ['id' => $otherTag->id]);
        foreach ($articles as $article) {
            $this->assertDatabaseHas('articles', ['id' => $article->id]);
        }

        // 確認其他標籤的關聯仍然正常
        $this->assertEquals(2, $otherTag->fresh()->articles()->count());
    }
} 