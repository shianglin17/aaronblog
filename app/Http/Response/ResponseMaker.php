<?php
namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseMaker
{
    private const SUCCESS_CODE = 200;

    /**
     * 成功回應
     * 
     * @param mixed|null $data 回應資料
     * @param mixed|null $meta 元資料
     * @param string $message 回應訊息
     * @return JsonResponse
     */
    public static function success($data = null, $meta = null, string $message = '成功'): JsonResponse
    {
        return response()->json(
            array_filter(
                [
                    'status' => 'success',
                    'code' => self::SUCCESS_CODE,
                    'message' => $message,
                    'data' => $data,
                    'meta' => $meta
                ],
                fn ($value) => $value !== null
            ),
            self::SUCCESS_CODE
        );
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
     * 
     * @param string $message 錯誤訊息
     * @param int $code HTTP 狀態碼
     * @return JsonResponse
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