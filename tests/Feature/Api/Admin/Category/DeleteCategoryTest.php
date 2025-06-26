<?php

namespace Tests\Feature\Api\Admin\Category;

use App\Models\Category;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * 分類刪除 API 測試（一般情況）
 * 
 * 測試範圍：
 * - DELETE /api/admin/categories/{id}
 * - 一般刪除情況（無約束限制）
 */
class DeleteCategoryTest extends AdminTestCase
{
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

    /**
     * 測試批量刪除分類
     */
    public function test_can_delete_multiple_categories(): void
    {
        $categories = Category::factory()->count(3)->create();

        foreach ($categories as $category) {
            $response = $this->deleteJson("/api/admin/categories/{$category->id}");
            $response->assertOk();
        }

        foreach ($categories as $category) {
            $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        }
    }
} 