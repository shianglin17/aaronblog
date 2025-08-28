<?php

namespace Tests\Feature\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Support\JsonAssertions;

/**
 * 管理員 API 測試基礎類別
 * 
 * 提供認證和 JSON 斷言輔助工具
 */
abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase, JsonAssertions;

    /**
     * 認證的使用者
     */
    protected User $authenticatedUser;

    /**
     * 在每個測試前執行
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // 建立並認證測試用戶（保持每個測試的獨立性）
        $this->authenticatedUser = User::factory()->create();
        $this->actingAs($this->authenticatedUser, 'web');
    }
} 