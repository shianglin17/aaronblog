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

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => '分類創建成功'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'slug',
                         'description',
                         'created_at'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    /**
     * 測試創建分類時的驗證
     */
    public function test_create_category_validation(): void
    {
        $response = $this->postJson('/api/admin/categories', []);

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

        $response->assertStatus(422);
    }
} 