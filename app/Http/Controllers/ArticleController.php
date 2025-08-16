<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Http\ApiResponse;
use App\Transformer\ArticleTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    /**
     * @param ArticleService $articleService
     * @param ArticleTransformer $articleTransformer
     */
    public function __construct(
        protected readonly ArticleService $articleService,
        protected readonly ArticleTransformer $articleTransformer
    ) {
    }

    /**
     * 取得文章列表
     * 
     * @OA\Get(
     *     path="/api/articles",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *     summary="獲取文章列表",
     *     description="獲取已發布的文章列表，支援分頁、排序、搜尋和篩選功能",
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
     *         @OA\Schema(type="string", enum={"created_at", "title"}, default="created_at", example="created_at")
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
     *         name="category",
     *         in="query",
     *         required=false,
     *         description="分類篩選",
     *         @OA\Schema(type="string", maxLength=255, example="tech-sharing")
     *     ),
     *     @OA\Parameter(
     *         name="tags",
     *         in="query",
     *         required=false,
     *         description="標籤篩選（最多5個）",
     *         @OA\Schema(type="array", maxItems=5, @OA\Items(type="string", maxLength=255, example="php"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取文章列表",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="文章列表"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Laravel 最佳實踐"),
     *                     @OA\Property(property="slug", type="string", example="laravel-best-practices"),
     *                     @OA\Property(property="description", type="string", example="分享 Laravel 開發的最佳實踐"),
     *                     @OA\Property(property="content", type="string", example="完整的文章內容..."),
     *                     @OA\Property(property="status", type="string", example="published"),
     *                     @OA\Property(
     *                         property="author",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Aaron")
     *                     ),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="技術分享"),
     *                         @OA\Property(property="slug", type="string", example="tech-sharing")
     *                     ),
     *                     @OA\Property(
     *                         property="tags",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="PHP"),
     *                             @OA\Property(property="slug", type="string", example="php")
     *                         )
     *                     ),
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
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=10)
     *             )
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
     *                 @OA\Property(property="per_page", type="array", @OA\Items(type="string", example="每頁筆數不能超過50"))
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
     * @param ListArticlesRequest $request
     * @return JsonResponse
     */
    public function index(ListArticlesRequest $request): JsonResponse
    {
        $articles = $this->articleService->getArticles($request->validated());

        return ApiResponse::paginated($articles, $this->articleTransformer);
    }

    /**
     * 取得單篇文章詳情
     * 
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     operationId="getArticleById",
     *     tags={"Articles"},
     *     summary="獲取單篇文章詳情",
     *     description="根據文章 ID 獲取單篇已發布文章的完整內容",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="文章 ID",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取文章詳情",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="文章詳情"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Laravel 最佳實踐"),
     *                 @OA\Property(property="slug", type="string", example="laravel-best-practices"),
     *                 @OA\Property(property="description", type="string", example="分享 Laravel 開發的最佳實踐"),
     *                 @OA\Property(property="content", type="string", example="完整的文章內容，包含 Markdown 格式..."),
     *                 @OA\Property(property="status", type="string", example="published"),
     *                 @OA\Property(
     *                     property="author",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Aaron")
     *                 ),
     *                 @OA\Property(
     *                     property="category",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="技術分享"),
     *                     @OA\Property(property="slug", type="string", example="tech-sharing")
     *                 ),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="PHP"),
     *                         @OA\Property(property="slug", type="string", example="php")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T14:30:00.000000Z")
     *             )
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
     * @param int $id 文章ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $article = $this->articleService->getArticleById($id);

        return ApiResponse::ok(
            $this->articleTransformer->transform($article)
        );
    }

}