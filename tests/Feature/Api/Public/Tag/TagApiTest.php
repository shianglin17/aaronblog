<?php

namespace Tests\Feature\Api\Public\Tag;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 公開標籤 API 測試
 * 
 * 測試範圍：
 * - GET /api/tags (列表)
 */
class TagApiTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * 測試取得標籤列表回傳正確格式
     */
    public function test_get_tags_list_returns_correct_format(): void
    {
        // Arrange: 建立測試標籤
        Tag::factory()->count(3)->create();

        // Act: 呼叫 API
        $response = $this->getJson('/api/tags');

        // Assert - 使用統一的成功斷言和列表結構驗證
        $this->assertApiSuccess($response, 200, '成功');
        $this->assertTagListStructure($response);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * 測試空標籤列表
     */
    public function test_get_empty_tags_list(): void
    {
        // Act: 呼叫 API（沒有建立任何標籤）
        $response = $this->getJson('/api/tags');

        // Assert: 驗證回應
        // Assert - 使用統一的成功斷言並驗證空列表
        $this->assertApiSuccess($response, 200, '成功');
        $response->assertJsonCount(0, 'data');
    }
} 