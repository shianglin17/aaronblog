<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 統一的 API 回應處理
 * 
 * 專為部落格 API 設計的簡潔回應格式
 */
class ApiResponse
{
    /**
     * 成功回應
     */
    public static function ok(mixed $data = null): JsonResponse
    {
        return response()->json(array_filter([
            'status' => 'success',
            'code' => 200,
            'message' => '成功',
            'data' => $data
        ], fn($value) => $value !== null));
    }

    /**
     * 創建成功 (201)
     */
    public static function created(mixed $data): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => '創建成功',
            'data' => $data
        ], 201);
    }

    /**
     * 分頁回應
     */
    public static function paginated(LengthAwarePaginator $paginator, object $transformer): JsonResponse
    {
        $items = collect($paginator->items());

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => '獲取列表成功',
            'data' => $transformer->transformCollection($items),
            'meta' => [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'total_pages' => $paginator->lastPage(),
                    'total_items' => $paginator->total(),
                    'per_page' => $paginator->perPage()
                ]
            ]
        ]);
    }

    /**
     * 驗證錯誤 (422)
     */
    public static function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => 422,
            'message' => '驗證錯誤',
            'meta' => ['errors' => $errors]
        ], 422);
    }

    /**
     * 找不到資源 (404) - 固定訊息
     */
    public static function notFound(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => 404,
            'message' => '資源不存在'
        ], 404);
    }

    /**
     * 資源使用中 (409)
     */
    public static function conflict(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], 409);
    }

    /**
     * 一般錯誤 (400)
     */
    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }
}