<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Transformer\CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    /**
     * @param CategoryTransformer $transformer
     * @param CategoryService $categoryService
     */
    public function __construct(
        protected readonly CategoryTransformer $transformer,
        protected readonly CategoryService $categoryService
    ) {
    }

    /**
     * 獲取所有分類
     * 
     * @OA\Get(
     *     path="/api/categories",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     summary="獲取所有分類",
     *     description="獲取系統中所有可用的文章分類，無需認證",
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取分類列表",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="所有分類"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1, description="分類 ID"),
     *                     @OA\Property(property="name", type="string", example="技術分享", description="分類名稱"),
     *                     @OA\Property(property="slug", type="string", example="tech-sharing", description="分類 Slug"),
     *                     @OA\Property(property="description", type="string", example="關於程式設計和技術的文章", description="分類描述"),
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
        $categories = $this->categoryService->getAllCategories();
        
        return ApiResponse::ok(
            $this->transformer->transformCollection($categories)
        );
    }

    /**
     * 獲取指定分類
     * 
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     operationId="getCategoryById",
     *     tags={"Categories"},
     *     summary="獲取指定分類",
     *     description="根據分類 ID 獲取單一分類的詳細資訊",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="分類 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取分類資訊",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="分類詳情"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="技術分享"),
     *                 @OA\Property(property="slug", type="string", example="tech-sharing"),
     *                 @OA\Property(property="description", type="string", example="關於程式設計和技術的文章"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="分類不存在",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     )
     * )
     * 
     * @param int $id 分類ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        
        return ApiResponse::ok(
            $this->transformer->transform($category)
        );
    }

    /**
     * 創建分類
     * 
     * @OA\Post(
     *     path="/api/admin/categories",
     *     operationId="createCategory",
     *     tags={"Admin", "Categories"},
     *     summary="創建分類",
     *     description="創建新的文章分類（僅限管理員）",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="分類資料",
     *         @OA\JsonContent(
     *             required={"name", "slug", "description"},
     *             @OA\Property(
     *                 property="name", 
     *                 type="string", 
     *                 maxLength=50, 
     *                 example="新技術", 
     *                 description="分類名稱（必填，最大50字元，不可重複）"
     *             ),
     *             @OA\Property(
     *                 property="slug", 
     *                 type="string", 
     *                 maxLength=255, 
     *                 pattern="^[a-zA-Z0-9_-]+$",
     *                 example="new-tech", 
     *                 description="分類 Slug（必填，最大255字元，只能包含字母、數字、底線、連字號，不可重複）"
     *             ),
     *             @OA\Property(
     *                 property="description", 
     *                 type="string", 
     *                 maxLength=500, 
     *                 example="關於新技術的文章分類", 
     *                 description="分類描述（必填，最大500字元）"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="分類創建成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="分類創建成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=4),
     *                 @OA\Property(property="name", type="string", example="新技術"),
     *                 @OA\Property(property="slug", type="string", example="new-tech"),
     *                 @OA\Property(property="description", type="string", example="關於新技術的文章分類"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T14:30:00.000000Z")
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
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="分類名稱為必填欄位")),
     *                 @OA\Property(property="slug", type="array", @OA\Items(type="string", example="分類 Slug 為必填欄位")),
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string", example="分類描述為必填欄位"))
     *             )
     *         )
     *     )
     * )
     * 
     * @param CreateCategoryRequest $request
     * @return JsonResponse
     */
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data);
        
        return ApiResponse::created(
            $this->transformer->transform($category)
        );
    }

    /**
     * 更新分類
     * 
     * @OA\Put(
     *     path="/api/admin/categories/{id}",
     *     operationId="updateCategory",
     *     tags={"Admin", "Categories"},
     *     summary="更新分類",
     *     description="更新指定分類的資訊（僅限管理員）",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="分類 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="更新的分類資料",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=50, example="進階技術", description="分類名稱（選填，最大50字元）"),
     *             @OA\Property(property="slug", type="string", maxLength=255, pattern="^[a-zA-Z0-9_-]+$", example="advanced-tech", description="分類 Slug（選填，最大255字元）"),
     *             @OA\Property(property="description", type="string", maxLength=500, example="進階技術相關文章", description="分類描述（選填，最大500字元）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="分類更新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="分類更新成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="進階技術"),
     *                 @OA\Property(property="slug", type="string", example="advanced-tech"),
     *                 @OA\Property(property="description", type="string", example="進階技術相關文章"),
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
     *         response=404,
     *         description="分類不存在",
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
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="分類名稱已被使用"))
     *             )
     *         )
     *     )
     * )
     * 
     * @param int $id 分類ID
     * @param UpdateCategoryRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(int $id, UpdateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->updateCategory($id, $data);
        
        return ApiResponse::ok(
            $this->transformer->transform($category)
        );
    }

    /**
     * 刪除分類
     * 
     * @OA\Delete(
     *     path="/api/admin/categories/{id}",
     *     operationId="deleteCategory",
     *     tags={"Admin", "Categories"},
     *     summary="刪除分類",
     *     description="刪除指定分類（僅限管理員，如分類下有文章則無法刪除）",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="分類 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="分類刪除成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="分類刪除成功"),
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
     *         description="分類不存在",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="分類正在使用中，無法刪除",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=409),
     *             @OA\Property(property="message", type="string", example="Cannot delete category that has articles")
     *         )
     *     )
     * )
     * 
     * @param int $id 分類ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws ResourceInUseException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        
        return ApiResponse::ok();
    }
} 