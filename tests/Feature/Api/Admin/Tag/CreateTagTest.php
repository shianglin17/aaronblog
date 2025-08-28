<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\Tag;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 標籤創建 API 測試
 * 
 * 測試範圍：
 * - POST /api/admin/tags
 */
class CreateTagTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試創建標籤
     */
    public function test_can_create_tag(): void
    {
        $tagData = [
            'name' => '測試標籤',
            'slug' => 'test-tag'
        ];

        $response = $this->postJson('/api/admin/tags', $tagData);

        // Assert - 使用統一的斷言方法
        $this->assertApiSuccess($response, 201, '創建成功');
        $this->assertTagResourceStructure($response);

        $this->assertDatabaseHas('tags', $tagData);
        
        // 驗證新創建的標籤 articles_count 為 0
        $responseData = $response->json('data');
        $this->assertEquals(0, $responseData['articles_count'], '新創建的標籤應該沒有關聯文章');
    }

    /**
     * 測試創建標籤時的驗證
     */
    public function test_create_tag_validation(): void
    {
        $response = $this->postJson('/api/admin/tags', []);

        // Assert - 使用統一的錯誤斷言
        $this->assertApiError($response, 422);
    }

    /**
     * 測試創建重複 slug 的標籤
     */
    public function test_cannot_create_tag_with_duplicate_slug(): void
    {
        // 先創建一個標籤
        Tag::factory()->create(['slug' => 'existing-slug']);

        // 嘗試創建相同 slug 的標籤
        $response = $this->postJson('/api/admin/tags', [
            'name' => '新標籤',
            'slug' => 'existing-slug'
        ]);

        // Assert - 驗證重複 slug 錯誤
        $this->assertApiError($response, 422);
    }

    /**
     * 測試創建包含特殊字符的標籤
     */
    public function test_can_create_tag_with_special_characters(): void
    {
        $tagData = [
            'name' => 'C++ 程式設計',
            'slug' => 'cpp-programming'
        ];

        $response = $this->postJson('/api/admin/tags', $tagData);

        // Assert - 簡化成功斷言
        $this->assertApiSuccess($response, 201, '創建成功');
        $this->assertDatabaseHas('tags', $tagData);
    }
} 