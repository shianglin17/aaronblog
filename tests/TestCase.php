<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    /**
     * 在每個測試前執行
     * 
     * 統一處理測試前的設定，確保測試隔離性
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // 清除所有快取，避免測試間的干擾
        Cache::flush();
    }
}
