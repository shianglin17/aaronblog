<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\Tag;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * 標籤刪除 API 測試
 * 
 * 測試範圍：
 * - DELETE /api/admin/tags/{id} (一般刪除情況)
 */
class DeleteTagTest extends AdminTestCase
{
    /**
     * 測試刪除標籤
     */
    public function test_can_delete_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->deleteJson("/api/admin/tags/{$tag->id}");

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => '標籤刪除成功'
                 ]);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /**
     * 測試刪除不存在的標籤
     */
    public function test_delete_non_existing_tag_returns_404(): void
    {
        $response = $this->deleteJson('/api/admin/tags/999');

        $response->assertNotFound();
    }

    /**
     * 測試批量刪除標籤
     */
    public function test_can_delete_multiple_tags(): void
    {
        $tags = Tag::factory()->count(3)->create();
        
        foreach ($tags as $tag) {
            $response = $this->deleteJson("/api/admin/tags/{$tag->id}");
            $response->assertOk()
                     ->assertJson([
                         'status' => 'success',
                         'message' => '標籤刪除成功'
                     ]);
        }

        foreach ($tags as $tag) {
            $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        }
    }
} 