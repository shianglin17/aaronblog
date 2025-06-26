<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 分類管理 API 測試
 * 
 * 測試範圍：
 * - POST /api/admin/categories (創建)
 * - PUT /api/admin/categories/{id} (更新)
 * - DELETE /api/admin/categories/{id} (刪除)
 */
class CategoryManagementTest extends AdminTestCase
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
     * 測試更新分類
     */
    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();
        
        $updateData = [
            'name' => '更新的分類名稱',
            'slug' => 'updated-category',
            'description' => '更新的描述'
        ];

        $response = $this->putJson("/api/admin/categories/{$category->id}", $updateData);

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => '分類更新成功'
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '更新的分類名稱'
        ]);
    }

    /**
     * 測試更新不存在的分類
     */
    public function test_update_non_existing_category_returns_404(): void
    {
        $response = $this->putJson('/api/admin/categories/999', [
            'name' => '測試分類'
        ]);

        $response->assertNotFound();
    }

    /**
     * 測試刪除分類
     */
    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => '分類刪除成功'
                 ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
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