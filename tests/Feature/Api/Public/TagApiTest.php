<?php

namespace Tests\Feature\Api\Public;

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
     * 在每個測試前執行
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // 清除所有快取，避免測試間的干擾
        \Illuminate\Support\Facades\Cache::flush();
    }

    /**
     * 測試取得標籤列表回傳正確格式
     */
    public function test_get_tags_list_returns_correct_format(): void
    {
        // Arrange: 建立測試標籤
        Tag::factory()->count(3)->create();

        // Act: 呼叫 API
        $response = $this->getJson('/api/tags');

        // Assert: 驗證回應格式
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'slug',
                             'created_at'
                         ]
                     ]
                 ])
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('code', 200)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * 測試空標籤列表
     */
    public function test_get_empty_tags_list(): void
    {
        // Act: 呼叫 API（沒有建立任何標籤）
        $response = $this->getJson('/api/tags');

        // Assert: 驗證回應
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('code', 200)
                 ->assertJsonCount(0, 'data');
    }
} 