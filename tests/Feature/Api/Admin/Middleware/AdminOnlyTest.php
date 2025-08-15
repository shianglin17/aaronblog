<?php

namespace Tests\Feature\Api\Admin\Middleware;

use App\Models\User;
use Tests\Feature\Api\Admin\AdminTestCase;

/**
 * AdminOnly Middleware 測試
 * 
 * 測試標籤和分類管理的權限限制
 */
class AdminOnlyTest extends AdminTestCase
{
    /**
     * 測試非主帳號無法創建標籤
     */
    public function test_non_admin_cannot_create_tag(): void
    {
        // 創建非主帳號用戶（ID 不是 1）
        $user = User::factory()->create(['id' => 999]);
        $this->actingAs($user);

        $response = $this->postJson('/api/admin/tags', [
            'name' => '測試標籤',
            'slug' => 'test-tag'
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'status' => 'error',
                     'code' => 403,
                     'message' => '權限不足，只有主帳號可以執行此操作'
                 ]);
    }

    /**
     * 測試非主帳號無法創建分類
     */
    public function test_non_admin_cannot_create_category(): void
    {
        // 創建非主帳號用戶
        $user = User::factory()->create(['id' => 999]);
        $this->actingAs($user);

        $response = $this->postJson('/api/admin/categories', [
            'name' => '測試分類',
            'slug' => 'test-category'
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'status' => 'error',
                     'code' => 403,
                     'message' => '權限不足，只有主帳號可以執行此操作'
                 ]);
    }

    /**
     * 測試主帳號可以創建標籤
     */
    public function test_admin_can_create_tag(): void
    {
        // 找到或創建 ID=1 的主帳號
        $admin = User::find(1) ?: User::factory()->create(['id' => 1, 'email' => 'admin@example.com']);
        $this->actingAs($admin);

        $response = $this->postJson('/api/admin/tags', [
            'name' => '管理員標籤',
            'slug' => 'admin-tag'
        ]);

        $response->assertStatus(201);
    }

    /**
     * 測試未登入用戶無法訪問
     */
    public function test_unauthenticated_user_cannot_access(): void
    {
        // 確保清除任何之前的認證狀態
        $this->app['auth']->forgetGuards();
        
        $response = $this->postJson('/api/admin/tags', [
            'name' => '測試標籤',
            'slug' => 'test-tag'
        ]);

        // 應該被外層 auth:web middleware 攔截，返回 401
        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.'
                 ]);
    }
}