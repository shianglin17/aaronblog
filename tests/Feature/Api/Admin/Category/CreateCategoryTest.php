<?php

namespace Tests\Feature\Api\Admin\Category;

use App\Models\Category;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 分類創建 API 測試
 * 
 * 測試範圍：
 * - POST /api/admin/categories
 */
class CreateCategoryTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試創建分類
     */
    public function test_can_create_category(): void
    {
        $categoryData = [
            'name' => '測試分類',
            'slug' => 'test-category',
            'description' => '這是一個測試分類'
        ];

        $response = $this->postJson('/api/admin/categories', $categoryData);

        // Assert - 使用統一的斷言方法
        $this->assertApiSuccess($response, 201, '創建成功');
        $this->assertCategoryResourceStructure($response);

        $this->assertDatabaseHas('categories', $categoryData);
        
        // 驗證新創建的分類 articles_count 為 0
        $responseData = $response->json('data');
        $this->assertEquals(0, $responseData['articles_count'], '新創建的分類應該沒有關聯文章');
    }

    /**
     * 測試創建分類時的驗證
     */
    public function test_create_category_validation(): void
    {
        $response = $this->postJson('/api/admin/categories', []);

        // Assert - 使用統一的錯誤斷言
        $this->assertApiError($response, 422);
    }

    /**
     * 測試創建重複 slug 的分類
     */
    public function test_cannot_create_category_with_duplicate_slug(): void
    {
        // 先創建一個分類
        Category::factory()->create(['slug' => 'existing-slug']);

        // 嘗試創建相同 slug 的分類
        $response = $this->postJson('/api/admin/categories', [
            'name' => '新分類',
            'slug' => 'existing-slug',
            'description' => '測試描述'
        ]);

        // Assert - 驗證重複 slug 錯誤
        $this->assertApiError($response, 422);
    }
} 