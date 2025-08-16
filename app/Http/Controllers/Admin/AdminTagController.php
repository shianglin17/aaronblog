<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\ApiResponse;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Services\TagService;
use App\Transformer\TagTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class AdminTagController extends Controller
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
     * 創建標籤（管理後台）
     * 
     * @OA\Post(
     *     path="/api/admin/tags",
     *     operationId="createTag",
     *     tags={"Admin"},
     *     summary="創建新標籤",
     *     description="創建新的文章標籤，需要管理員權限",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="標籤資料",
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="PHP", description="標籤名稱（唯一）"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="php", description="標籤 slug（唯一，只能包含字母、數字、破折號和底線）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="標籤創建成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="PHP"),
     *                 @OA\Property(property="slug", type="string", example="php"),
     *                 @OA\Property(property="articles_count", type="integer", example=0),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="未授權",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="驗證失敗",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="標籤名稱已被使用")),
     *                 @OA\Property(property="slug", type="array", @OA\Items(type="string", example="slug 已被使用"))
     *             )
     *         )
     *     )
     * )
     * 
     * @param CreateTagRequest $request
     * @return JsonResponse
     */
    public function store(CreateTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tag = $this->tagService->createTag($data);
        
        return ApiResponse::created(
            $this->transformer->transform($tag)
        );
    }

    /**
     * 更新標籤（管理後台）
     * 
     * @OA\Put(
     *     path="/api/admin/tags/{id}",
     *     operationId="updateTag",
     *     tags={"Admin"},
     *     summary="更新標籤",
     *     description="更新指定的標籤資訊，需要管理員權限",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="標籤 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="更新的標籤資料（部分更新）",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="PHP Framework", description="標籤名稱（可選）"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="php-framework", description="標籤 slug（可選）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="標籤更新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="PHP Framework"),
     *                 @OA\Property(property="slug", type="string", example="php-framework"),
     *                 @OA\Property(property="articles_count", type="integer", example=5),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T14:30:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="未授權",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
     *         response=422,
     *         description="驗證失敗",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="The given data was invalid")
     *         )
     *     )
     * )
     * 
     * @param int $id 標籤ID
     * @param UpdateTagRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(int $id, UpdateTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tag = $this->tagService->updateTag($id, $data);
        
        return ApiResponse::ok(
            $this->transformer->transform($tag)
        );
    }

    /**
     * 刪除標籤（管理後台）
     * 
     * @OA\Delete(
     *     path="/api/admin/tags/{id}",
     *     operationId="deleteTag",
     *     tags={"Admin"},
     *     summary="刪除標籤",
     *     description="刪除指定的標籤，需要管理員權限",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="標籤 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="標籤刪除成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="未授權",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
     *         response=409,
     *         description="無法刪除（標籤仍被文章使用）",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=409),
     *             @OA\Property(property="message", type="string", example="Cannot delete tag with associated articles")
     *         )
     *     )
     * )
     * 
     * @param int $id 標籤ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->tagService->deleteTag($id);
        
        return ApiResponse::ok();
    }
}