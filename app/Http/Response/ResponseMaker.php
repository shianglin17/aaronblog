<?php
namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseMaker
{
    private const SUCCESS_CODE = 200;
    private const ERROR_CODE = 400;

    /**
     * 成功回應
     */
    public static function success($data = null, $meta = null, string $message = '成功'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => self::SUCCESS_CODE,
            'message' => $message,
            'data' => $data,
            'meta' => $meta
        ], self::SUCCESS_CODE);
    }

    /**
     * 使用 Transformer 處理分頁資料
     *
     * @param LengthAwarePaginator $paginator 分頁資料
     * @param object $transformer 轉換器實例
     * @return JsonResponse
     */
    public static function paginatorWithTransformer(LengthAwarePaginator $paginator, object $transformer): JsonResponse
    {
        $items = collect($paginator->items());
        
        return self::success(
            $transformer->transformCollection($items),
            [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'total_pages' => $paginator->lastPage(),
                    'total_items' => $paginator->total(),
                    'per_page' => $paginator->perPage()
                ]
            ]
        );
    }

    /**
     * 錯誤回應
     */
    public static function error(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => self::ERROR_CODE,
            'message' => $message
        ], self::ERROR_CODE);
    }
}