<?php

namespace Tests\Feature\Api\Admin\Category;

use App\Models\Category;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * 分類已發布文章計數測試
 * 
 * 測試 articles_count 只包含已發布的文章
 */
class CategoryPublishedArticlesCountTest extends AdminTestCase
{
    /**
     * 測試分類計數只包含已發布文章
     */
    public function test_category_articles_count_only_includes_published_articles(): void
    {
        // 建立測試資料
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        // 創建 2 篇已發布文章
        Article::factory()->count(2)->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'published'
        ]);
        
        // 創建 3 篇草稿文章
        Article::factory()->count(3)->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'draft'
        ]);

        // 獲取分類列表
        $response = $this->getJson('/api/categories');

        // Assert - 使用統一的成功斷言（公開 API）
        $this->assertApiSuccess($response, 200, '成功');
        
        $categoryData = collect($response->json('data'))
            ->firstWhere('id', $category->id);
            
        // 驗證只計算已發布文章
        $this->assertEquals(2, $categoryData['articles_count'], '分類應該只計算已發布文章的數量');
    }

    /**
     * 測試更新分類後文章計數正確
     */
    public function test_update_category_articles_count_only_includes_published(): void
    {
        // 建立測試資料
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => '原始分類']);
        
        // 創建 4 篇已發布文章和 1 篇草稿文章
        Article::factory()->count(4)->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'published'
        ]);
        
        Article::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'draft'
        ]);

        // 更新分類
        $response = $this->putJson("/api/admin/categories/{$category->id}", [
            'name' => '更新的分類名稱'
        ]);

        // Assert - 使用統一的成功斷言
        $this->assertApiSuccess($response, 200, '成功');
        
        $responseData = $response->json('data');
        $this->assertEquals(4, $responseData['articles_count'], '更新分類時應該只計算已發布文章');
    }
}