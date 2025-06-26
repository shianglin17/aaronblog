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

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => '標籤創建成功'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'slug',
                         'created_at'
                     ]
                 ]);

        $this->assertDatabaseHas('tags', $tagData);
    }

    /**
     * 測試創建標籤時的驗證
     */
    public function test_create_tag_validation(): void
    {
        $response = $this->postJson('/api/admin/tags', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'meta' => [
                         'errors'
                     ]
                 ]);
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

        $response->assertStatus(422);
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

        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', $tagData);
    }
} 