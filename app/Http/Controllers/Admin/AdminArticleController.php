<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthenticatedUser;
use App\Services\AdminArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\AdminListArticlesRequest;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\ApiResponse;
use App\Transformer\ArticleTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class AdminArticleController extends Controller
{
    use AuthenticatedUser;

    /**
     * @param AdminArticleService $adminArticleService
     * @param ArticleTransformer $articleTransformer
     */
    public function __construct(
        protected readonly AdminArticleService $adminArticleService,
        protected readonly ArticleTransformer $articleTransformer
    ) {
    }

    /**
     * 取得當前用戶的文章列表（管理後台）
     * 
     * @OA\Get(
     *     path="/api/admin/articles",
     *     operationId="getAdminArticles",
     *     tags={"Admin"},
     *     summary="獲取當前用戶的文章列表",
     *     description="獲取當前登入用戶的所有文章，包含草稿和已發布的文章，支援分頁、排序、搜尋功能",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="頁碼",
     *         @OA\Schema(type="integer", minimum=1, default=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="每頁筆數",
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="排序欄位",
     *         @OA\Schema(type="string", enum={"created_at", "title", "status"}, default="created_at", example="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         required=false,
     *         description="排序方向",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc", example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="搜尋關鍵字（標題、內容）",
     *         @OA\Schema(type="string", maxLength=100, example="Laravel")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="文章狀態篩選",
     *         @OA\Schema(type="string", enum={"draft", "published"}, example="published")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取文章列表",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="用戶文章列表"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Laravel 最佳實踐"),
     *                     @OA\Property(property="slug", type="string", example="laravel-best-practices"),
     *                     @OA\Property(property="description", type="string", example="分享 Laravel 開發的最佳實踐"),
     *                     @OA\Property(property="status", type="string", example="published"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T14:30:00.000000Z")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25),
     *                 @OA\Property(property="last_page", type="integer", example=3)
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
     *             @OA\Property(property="message", type="string", example="The given data was invalid")
     *         )
     *     )
     * )
     * 
     * @param AdminListArticlesRequest $request
     * @return JsonResponse
     */
    public function index(AdminListArticlesRequest $request): JsonResponse
    {
        $params = $this->withCurrentUserParams($request->validatedWithDefaults());
        $articles = $this->adminArticleService->getUserArticles($params);

        return ApiResponse::paginated($articles, $this->articleTransformer);
    }

    /**
     * 創建文章（管理後台）
     * 
     * @OA\Post(
     *     path="/api/admin/articles",
     *     operationId="createAdminArticle",
     *     tags={"Admin"},
     *     summary="創建新文章",
     *     description="創建新的文章，歸屬於當前登入用戶",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="文章資料",
     *         @OA\JsonContent(
     *             required={"title", "slug", "description", "content", "status"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Laravel 最佳實踐", description="文章標題（唯一）"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="laravel-best-practices", description="URL slug（唯一，只能包含字母、數字、破折號和底線）"),
     *             @OA\Property(property="description", type="string", maxLength=255, example="分享 Laravel 開發的最佳實踐", description="文章描述"),
     *             @OA\Property(property="content", type="string", example="# Laravel 最佳實踐\n\n這是文章內容...", description="文章內容（支援 Markdown）"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="分類 ID（可選）"),
     *             @OA\Property(property="status", type="string", enum={"draft", "published"}, example="published", description="文章狀態"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer", example=1), description="標籤 ID 陣列（可選）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="文章創建成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Laravel 最佳實踐"),
     *                 @OA\Property(property="slug", type="string", example="laravel-best-practices"),
     *                 @OA\Property(property="description", type="string", example="分享 Laravel 開發的最佳實踐"),
     *                 @OA\Property(property="content", type="string", example="# Laravel 最佳實踐..."),
     *                 @OA\Property(property="status", type="string", example="published"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z")
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
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="標題已被使用")),
     *                 @OA\Property(property="slug", type="array", @OA\Items(type="string", example="slug 已被使用"))
     *             )
     *         )
     *     )
     * )
     *
     * @param CreateArticleRequest $request
     * @return JsonResponse
     */
    public function store(CreateArticleRequest $request): JsonResponse
    {
        $data = $this->withCurrentUser($request->validated());
        
        $article = $this->adminArticleService->createUserArticle($data);

        return ApiResponse::created(
            $this->articleTransformer->transform($article)
        );
    }

    /**
     * 更新當前用戶的文章（管理後台）
     * 
     * @OA\Put(
     *     path="/api/admin/articles/{id}",
     *     operationId="updateAdminArticle",
     *     tags={"Admin"},
     *     summary="更新文章",
     *     description="更新當前用戶的指定文章",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="文章 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="更新的文章資料（部分更新）",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="Laravel 最佳實踐（更新版）", description="文章標題（可選）"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="laravel-best-practices-updated", description="URL slug（可選）"),
     *             @OA\Property(property="description", type="string", maxLength=255, example="更新的文章描述", description="文章描述（可選）"),
     *             @OA\Property(property="content", type="string", example="# Laravel 最佳實踐\n\n更新的文章內容...", description="文章內容（可選）"),
     *             @OA\Property(property="category_id", type="integer", example=2, description="分類 ID（可選）"),
     *             @OA\Property(property="status", type="string", enum={"draft", "published"}, example="published", description="文章狀態（可選）"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer", example=2), description="標籤 ID 陣列（可選）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="文章更新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Laravel 最佳實踐（更新版）"),
     *                 @OA\Property(property="slug", type="string", example="laravel-best-practices-updated"),
     *                 @OA\Property(property="description", type="string", example="更新的文章描述"),
     *                 @OA\Property(property="content", type="string", example="# Laravel 最佳實踐..."),
     *                 @OA\Property(property="status", type="string", example="published"),
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
     *         response=403,
     *         description="權限不足（非文章作者）",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=403),
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="文章不存在",
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
     * @param int $id 文章ID
     * @param UpdateArticleRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(int $id, UpdateArticleRequest $request): JsonResponse
    {
        $article = $this->adminArticleService->updateUserArticle($id, $request->validated(), $this->getCurrentUserId());

        return ApiResponse::ok(
            $this->articleTransformer->transform($article)
        );
    }

    /**
     * 刪除當前用戶的文章（管理後台）
     * 
     * @OA\Delete(
     *     path="/api/admin/articles/{id}",
     *     operationId="deleteAdminArticle",
     *     tags={"Admin"},
     *     summary="刪除文章",
     *     description="刪除當前用戶的指定文章",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="文章 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="文章刪除成功",
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
     *         response=403,
     *         description="權限不足（非文章作者）",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=403),
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="文章不存在",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     )
     * )
     *
     * @param int $id 文章ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->adminArticleService->deleteUserArticle($id, $this->getCurrentUserId());

        return ApiResponse::ok();
    }
}