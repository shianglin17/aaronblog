<?php

namespace Tests\Feature\Api\Public\Category;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 公開分類 API 測試
 * 
 * 測試範圍：
 * - GET /api/categories (列表)
 */
class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試取得分類列表回傳正確格式
     */
    public function test_get_categories_list_returns_correct_format(): void
    {
        // Arrange: 建立測試分類
        Category::factory()->count(3)->create();

        // Act: 呼叫 API
        $response = $this->getJson('/api/categories');

        // Assert - 使用統一的成功斷言和列表結構驗證
        $this->assertApiSuccess($response, 200, '成功');
        $this->assertCategoryListStructure($response);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * 測試空分類列表
     */
    public function test_get_empty_categories_list(): void
    {
        // Act: 呼叫 API（沒有建立任何分類）
        $response = $this->getJson('/api/categories');

        // Assert - 使用統一的成功斷言並驗證空列表
        $this->assertApiSuccess($response, 200, '成功');
        $response->assertJsonCount(0, 'data');
    }
} 