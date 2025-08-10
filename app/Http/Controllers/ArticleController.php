<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Http\Requests\Article\GetArticleRequest;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\ApiResponse;
use App\Transformer\ArticleTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

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
     * @param ListArticlesRequest $request
     * 
     * @return JsonResponse
     */
    public function index(ListArticlesRequest $request): JsonResponse
    {
        $articles = $this->articleService->getArticles($request->validated());

        return ApiResponse::paginated($articles, $this->articleTransformer);
    }

    /**
     * 取得單篇文章詳情
     * @param int $id 文章ID
     * 
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

    /**
     * 創建文章
     * @param CreateArticleRequest $request
     * 
     * @return JsonResponse
     */
    public function store(CreateArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        
        $article = $this->articleService->createArticle($data);

        return ApiResponse::created(
            $this->articleTransformer->transform($article)
        );
    }

    /**
     * 更新文章
     * @param int $id 文章ID
     * @param UpdateArticleRequest $request
     * 
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(int $id, UpdateArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->updateArticle($id, $request->validated());

        return ApiResponse::ok(
            $this->articleTransformer->transform($article)
        );
    }

    /**
     * 刪除文章
     * @param int $id 文章ID
     * 
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->articleService->deleteArticle($id);

        return ApiResponse::ok();
    }
}