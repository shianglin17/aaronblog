<?php
namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseMaker
{
    /**
     * 成功回應
     */
    public static function success($data = null, string $message = '成功', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * 處理分頁資料
     */
    public static function paginator(LengthAwarePaginator $paginator): JsonResponse
    {
        return self::success([
            'items' => $paginator->items(),
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'totalPages' => $paginator->lastPage(),
                'totalItems' => $paginator->total(),
                'perPage' => $paginator->perPage()
            ]
        ]);
    }

    /**
     * 錯誤回應
     */
    public static function error(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ], $code);
    }
}