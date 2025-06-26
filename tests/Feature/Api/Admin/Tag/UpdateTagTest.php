<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\Tag;
use Tests\Feature\Api\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 標籤更新 API 測試
 * 
 * 測試範圍：
 * - PUT /api/admin/tags/{id}
 */
class UpdateTagTest extends AdminTestCase
{
    use WithFaker;

    /**
     * 測試更新標籤
     */
    public function test_can_update_tag(): void
    {
        $tag = Tag::factory()->create();
        
        $updateData = [
            'name' => '更新的標籤名稱',
            'slug' => 'updated-tag'
        ];

        $response = $this->putJson("/api/admin/tags/{$tag->id}", $updateData);

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => '標籤更新成功'
                 ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '更新的標籤名稱'
        ]);
    }

    /**
     * 測試更新不存在的標籤
     */
    public function test_update_non_existing_tag_returns_404(): void
    {
        $response = $this->putJson('/api/admin/tags/999', [
            'name' => '測試標籤'
        ]);

        $response->assertNotFound();
    }

    /**
     * 測試部分更新標籤
     */
    public function test_can_partially_update_tag(): void
    {
        $tag = Tag::factory()->create([
            'name' => '原始名稱',
            'slug' => 'original-slug'
        ]);

        // 只更新名稱
        $response = $this->putJson("/api/admin/tags/{$tag->id}", [
            'name' => '新名稱'
        ]);

        $response->assertOk();
        
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '新名稱',
            'slug' => 'original-slug' // 保持不變
        ]);
    }

    /**
     * 測試更新為重複 slug
     */
    public function test_cannot_update_to_duplicate_slug(): void
    {
        $existingTag = Tag::factory()->create(['slug' => 'existing-slug']);
        $tagToUpdate = Tag::factory()->create(['slug' => 'original-slug']);

        $response = $this->putJson("/api/admin/tags/{$tagToUpdate->id}", [
            'name' => '新名稱',
            'slug' => 'existing-slug' // 嘗試使用已存在的 slug
        ]);

        $response->assertStatus(422);
    }
} 