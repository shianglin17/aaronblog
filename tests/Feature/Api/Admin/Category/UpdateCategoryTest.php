<?php

namespace Tests\Feature\Api\Admin\Category;

use App\Models\Category;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 分類更新 API 測試
 * 
 * 測試範圍：
 * - PUT /api/admin/categories/{id}
 */
class UpdateCategoryTest extends AdminTestCase
{
    use WithFaker;

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
     * 測試部分更新分類
     */
    public function test_can_partially_update_category(): void
    {
        $category = Category::factory()->create([
            'name' => '原始名稱',
            'description' => '原始描述'
        ]);

        // 只更新名稱
        $response = $this->putJson("/api/admin/categories/{$category->id}", [
            'name' => '新名稱'
        ]);

        $response->assertOk();
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '新名稱',
            'description' => '原始描述' // 保持不變
        ]);
    }
} 