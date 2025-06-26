<?php

namespace Tests\Feature\Api\Public;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Illuminate\Support\Facades\Cache;

/**
 * 通用 API 測試
 * 
 * 測試範圍：
 * - API 根路徑
 * - 資料庫隔離
 * - 快取隔離
 * - 通用功能測試
 */
class GeneralApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試 API 根路徑回傳工作訊息
     */
    public function test_api_root_returns_working_message(): void
    {
        $response = $this->getJson('/api');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message'
                 ])
                 ->assertJsonPath('message', 'API is working');
    }

    /**
     * 測試資料庫隔離 - 第一個測試
     */
    public function test_database_isolation_first_test(): void
    {
        // Arrange: 建立文章
        Article::factory()->published()->create(['title' => 'First Test Article']);

        // Assert: 確認文章存在
        $this->assertDatabaseCount('articles', 1);
        $this->assertDatabaseHas('articles', ['title' => 'First Test Article']);
    }

    /**
     * 測試資料庫隔離 - 第二個測試
     * 這個測試應該看不到前一個測試的資料
     */
    public function test_database_isolation_second_test(): void
    {
        // Assert: 確認資料庫是乾淨的
        $this->assertDatabaseCount('articles', 0);
        $this->assertDatabaseMissing('articles', ['title' => 'First Test Article']);

        // Arrange: 建立新文章
        Article::factory()->published()->create(['title' => 'Second Test Article']);

        // Assert: 確認只有新文章
        $this->assertDatabaseCount('articles', 1);
        $this->assertDatabaseHas('articles', ['title' => 'Second Test Article']);
    }

    /**
     * 測試快取隔離
     */
    public function test_cache_isolation(): void
    {
        // Arrange: 設定快取
        Cache::put('test_key', 'test_value', 60);

        // Assert: 確認快取存在
        $this->assertEquals('test_value', Cache::get('test_key'));

        // 注意：快取會在 setUp() 中被清除，所以每個測試都是乾淨的
    }
} 