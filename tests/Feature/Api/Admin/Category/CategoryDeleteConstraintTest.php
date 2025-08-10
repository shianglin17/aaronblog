<?php

namespace Tests\Feature\Api\Admin\Category;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CategoryDeleteConstraintTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試刪除沒有文章使用的分類（應該成功）
     */
    public function test_can_delete_category_when_no_articles_using_it(): void
    {
        // 建立一個沒有文章的分類
        $category = Category::factory()->create();

        // 嘗試刪除分類
        $response = $this->deleteJson("/api/admin/categories/{$category->id}");

        // 應該成功刪除
        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => '成功'
        ]);

        // 確認分類已被刪除
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * 測試刪除有文章使用的分類（應該失敗）
     */
    public function test_cannot_delete_category_when_articles_using_it(): void
    {
        // 建立分類和使用該分類的文章
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        Article::factory()->count(3)->create([
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        // 嘗試刪除分類
        $response = $this->deleteJson("/api/admin/categories/{$category->id}");

        // 應該返回錯誤
        $response->assertStatus(409);
        $response->assertJson([
            'status' => 'error',
            'message' => "無法刪除 分類（ID: {$category->id}），因為仍有 3 篇文章正在使用此分類"
        ]);

        // 確認分類仍然存在
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /**
     * 測試刪除不存在的分類
     */
    public function test_delete_non_existing_category_returns_404(): void
    {
        $response = $this->deleteJson('/api/admin/categories/999');

        $response->assertNotFound();
    }
} 