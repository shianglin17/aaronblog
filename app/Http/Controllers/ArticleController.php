<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Http\ApiResponse;
use App\Transformer\ArticleTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

}