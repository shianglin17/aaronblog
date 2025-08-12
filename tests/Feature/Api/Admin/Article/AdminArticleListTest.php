<?php

namespace Tests\Feature\Api\Admin\Article;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * 管理後台文章列表 API 測試
 * 
 * 測試範圍：
 * - GET /api/admin/articles
 * - 用戶隔離功能
 * - 狀態過濾功能
 * - AdminArticleController::index()
 */
class AdminArticleListTest extends AdminTestCase
{
    /**
     * 測試用戶只能看到自己的文章
     */
    public function test_user_can_only_see_own_articles(): void
    {
        // Arrange: 建立其他用戶和文章
        $otherUser = User::factory()->create();
        $otherUserArticles = Article::factory()->count(3)->create(['user_id' => $otherUser->id]);
        
        // 建立當前用戶的文章
        $myArticles = Article::factory()->count(2)->create(['user_id' => $this->authenticatedUser->id]);

        // Act: 呼叫 API
        $response = $this->getJson('/api/admin/articles');

        // Assert: 只能看到自己的文章
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // 只有2篇自己的文章

        // 確認回傳的文章都屬於當前用戶
        $responseData = $response->json('data');
        foreach ($responseData as $article) {
            $this->assertEquals($this->authenticatedUser->id, $article['author']['id']);
        }
    }

    /**
     * 測試管理後台可以看到所有狀態的文章
     */
    public function test_admin_can_see_all_status_articles(): void
    {
        // Arrange: 建立不同狀態的文章
        Article::factory()->create([
            'user_id' => $this->authenticatedUser->id,
            'status' => 'published'
        ]);
        Article::factory()->create([
            'user_id' => $this->authenticatedUser->id,
            'status' => 'draft'
        ]);

        // Act: 呼叫 API
        $response = $this->getJson('/api/admin/articles');

        // Assert: 可以看到所有狀態
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    /**
     * 測試狀態過濾功能
     */
    public function test_can_filter_by_status(): void
    {
        // Arrange: 建立不同狀態的文章
        Article::factory()->create([
            'user_id' => $this->authenticatedUser->id,
            'status' => 'published'
        ]);
        Article::factory()->create([
            'user_id' => $this->authenticatedUser->id,
            'status' => 'draft'
        ]);

        // Act & Assert: 測試只看已發布的文章
        $response = $this->getJson('/api/admin/articles?status=published');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');

        // Act & Assert: 測試只看草稿文章
        $response = $this->getJson('/api/admin/articles?status=draft');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    /**
     * 測試需要認證
     */
    public function test_requires_authentication(): void
    {
        // Arrange: 登出當前用戶
        $this->post('/api/auth/logout');
        
        // Act: 未認證呼叫 API
        $response = $this->getJson('/api/admin/articles');

        // Assert: 回傳未授權錯誤
        $response->assertStatus(401);
    }

    /**
     * 測試分頁功能
     */
    public function test_supports_pagination(): void
    {
        // Arrange: 建立20篇文章
        Article::factory()->count(20)->create(['user_id' => $this->authenticatedUser->id]);

        // Act: 呼叫 API 要求第2頁，每頁10筆
        $response = $this->getJson('/api/admin/articles?page=2&per_page=10');

        // Assert: 驗證分頁回應格式
        $response->assertStatus(200)
                 ->assertJsonCount(10, 'data')
                 ->assertJsonStructure([
                     'meta' => [
                         'pagination' => [
                             'current_page',
                             'total_pages',
                             'total_items',
                             'per_page'
                         ]
                     ]
                 ])
                 ->assertJsonPath('meta.pagination.current_page', 2)
                 ->assertJsonPath('meta.pagination.per_page', 10)
                 ->assertJsonPath('meta.pagination.total_items', 20);
    }
}