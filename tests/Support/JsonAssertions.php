<?php

namespace Tests\Support;

/**
 * JSON 斷言輔助工具
 * 
 * 提供統一的 API 回應斷言方法，消除重複代碼
 */
trait JsonAssertions
{
    /**
     * 斷言標準 API 成功回應格式
     */
    protected function assertApiSuccess($response, int $statusCode = 200, string $message = '操作成功'): void
    {
        $response->assertStatus($statusCode)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'data'
                 ])
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('code', $statusCode);
                 
        if ($message) {
            $response->assertJsonPath('message', $message);
        }
    }

    /**
     * 斷言標準 API 錯誤回應格式
     */
    protected function assertApiError($response, int $statusCode = 422): void
    {
        $response->assertStatus($statusCode);
        
        // 檢查不同的錯誤回應格式
        if ($response->json('meta.errors')) {
            // 自定義 API 格式
            $response->assertJsonStructure([
                'status',
                'code',
                'message',
                'meta' => ['errors']
            ]);
        } else {
            // Laravel 標準格式
            $response->assertJsonStructure([
                'message',
                'errors'
            ]);
        }
    }

    /**
     * 斷言驗證錯誤包含指定欄位
     */
    protected function assertValidationErrors($response, array $fields): void
    {
        $response->assertStatus(422);
        
        if ($response->json('meta.errors')) {
            $errors = $response->json('meta.errors');
            foreach ($fields as $field) {
                $this->assertArrayHasKey($field, $errors, "驗證錯誤應包含欄位: {$field}");
            }
        } else {
            $response->assertJsonValidationErrors($fields);
        }
    }

    /**
     * 斷言標籤資源結構
     */
    protected function assertTagResourceStructure($response): void
    {
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'articles_count',
                'created_at'
            ]
        ]);
    }

    /**
     * 斷言分類資源結構
     */
    protected function assertCategoryResourceStructure($response): void
    {
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'articles_count',
                'created_at'
            ]
        ]);
    }

    /**
     * 斷言文章資源結構
     */
    protected function assertArticleResourceStructure($response): void
    {
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'slug',
                'description',
                'content',
                'status',
                'created_at',
                'updated_at',
                'author' => [
                    'id',
                    'name'
                ],
                'category' => [
                    'id',
                    'name',
                    'slug'
                ],
                'tags' => [
                    '*' => [
                        'id',
                        'name',
                        'slug'
                    ]
                ]
            ]
        ]);
    }

    /**
     * 斷言文章列表結構
     */
    protected function assertArticleListStructure($response): void
    {
        $response->assertJsonStructure([
            'status',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'content',
                    'status',
                    'created_at',
                    'updated_at',
                    'author' => ['id', 'name'],
                    'category' => ['id', 'name', 'slug'],
                    'tags' => [
                        '*' => ['id', 'name', 'slug']
                    ]
                ]
            ],
            'meta' => [
                'pagination' => [
                    'current_page',
                    'total_pages',
                    'total_items',
                    'per_page'
                ]
            ]
        ]);
    }

    /**
     * 斷言標籤列表結構
     */
    protected function assertTagListStructure($response): void
    {
        $response->assertJsonStructure([
            'status',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'articles_count',
                    'created_at'
                ]
            ]
        ]);
    }

    /**
     * 斷言分類列表結構
     */
    protected function assertCategoryListStructure($response): void
    {
        $response->assertJsonStructure([
            'status',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'articles_count',
                    'created_at'
                ]
            ]
        ]);
    }
}