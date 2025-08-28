<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 認證 API 測試
 * 
 * 測試範圍：
 * - POST /api/auth/login
 * - POST /api/auth/register  
 * - POST /api/auth/logout
 * - GET /api/auth/user
 */
class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試用戶登入成功
     */
    public function test_user_can_login_successfully(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email']
                     ]
                 ]);
    }

    /**
     * 測試錯誤憑證登入失敗
     */
    public function test_login_fails_with_wrong_credentials(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * 測試登入需要必填欄位
     */
    public function test_login_requires_email_and_password(): void
    {
        // Act
        $response = $this->postJson('/api/auth/login', []);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * 測試註冊端點存在性
     * 由於註冊需要邀請碼，這裡測試端點基本回應
     */
    public function test_registration_endpoint_exists(): void
    {
        // Act - 不提供任何數據來測試端點是否存在
        $response = $this->postJson('/api/auth/register', []);

        // Assert - 應該返回驗證錯誤而不是 404
        $response->assertStatus(422);
    }

    /**
     * 測試已認證用戶可以登出
     */
    public function test_authenticated_user_can_logout(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        // Act
        $response = $this->postJson('/api/auth/logout');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success');
    }

    /**
     * 測試未認證用戶無法登出
     */
    public function test_unauthenticated_user_cannot_logout(): void
    {
        // Act
        $response = $this->postJson('/api/auth/logout');

        // Assert
        $response->assertStatus(401);
    }

    /**
     * 測試取得當前認證用戶資訊
     */
    public function test_can_get_current_user_info(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com'
        ]);
        $this->actingAs($user, 'web');

        // Act
        $response = $this->getJson('/api/auth/user');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('data.name', 'Test User')
                 ->assertJsonPath('data.email', 'user@example.com');
    }

    /**
     * 測試未認證用戶無法取得用戶資訊
     */
    public function test_unauthenticated_user_cannot_get_user_info(): void
    {
        // Act
        $response = $this->getJson('/api/auth/user');

        // Assert
        $response->assertStatus(401);
    }
}