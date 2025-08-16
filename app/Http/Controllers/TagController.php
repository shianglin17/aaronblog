<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Services\TagService;
use App\Transformer\TagTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class TagController extends Controller
{
    /**
     * @param TagTransformer $transformer
     * @param TagService $tagService
     */
    public function __construct(
        protected readonly TagTransformer $transformer,
        protected readonly TagService $tagService
    ) {
    }

    /**
     * 獲取所有標籤
     * 
     * @OA\Get(
     *     path="/api/tags",
     *     operationId="getTags",
     *     tags={"Tags"},
     *     summary="獲取所有標籤",
     *     description="獲取系統中所有可用的文章標籤，包含文章數量統計",
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取標籤列表",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="所有標籤"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1, description="標籤 ID"),
     *                     @OA\Property(property="name", type="string", example="PHP", description="標籤名稱"),
     *                     @OA\Property(property="slug", type="string", example="php", description="標籤 Slug"),
     *                     @OA\Property(property="articles_count", type="integer", example=5, description="使用此標籤的文章數量"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z", description="建立時間")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="請求過於頻繁",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=429),
     *             @OA\Property(property="message", type="string", example="Too Many Requests")
     *         )
     *     )
     * )
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags();
        
        return ApiResponse::ok(
            $this->transformer->transformCollection($tags)
        );
    }

    /**
     * 獲取指定標籤
     * 
     * @OA\Get(
     *     path="/api/tags/{id}",
     *     operationId="getTagById",
     *     tags={"Tags"},
     *     summary="獲取指定標籤",
     *     description="根據標籤 ID 獲取單一標籤的詳細資訊，包含文章數量統計",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="標籤 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取標籤資訊",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="標籤詳情"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="PHP"),
     *                 @OA\Property(property="slug", type="string", example="php"),
     *                 @OA\Property(property="articles_count", type="integer", example=5),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="標籤不存在",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="請求過於頻繁",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=429),
     *             @OA\Property(property="message", type="string", example="Too Many Requests")
     *         )
     *     )
     * )
     * 
     * @param int $id 標籤ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->getTagById($id);
        
        return ApiResponse::ok(
            $this->transformer->transform($tag)
        );
    }

} 