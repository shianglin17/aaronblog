<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Http\Requests\Article\GetArticleRequest;
use App\Http\Response\ResponseMaker;
use App\Transformer\ArticleTransformer;

class ArticleController extends Controller
{
    protected readonly ArticleService $articleService;
    protected readonly ArticleTransformer $articleTransformer;

    public function __construct(
        ArticleService $articleService,
        ArticleTransformer $articleTransformer
    ) {
        $this->articleService = $articleService;
        $this->articleTransformer = $articleTransformer;
    }

    /**
     * 取得文章列表
     * @param ListArticlesRequest $request
     * 
     * @return JsonResponse
     */
    public function list(ListArticlesRequest $request): JsonResponse
    {
        $param = $request->validated();
        $articles = $this->articleService->getArticles($param);

        return ResponseMaker::paginatorWithTransformer($articles, $this->articleTransformer);
    }

    /**
     * 取得單篇文章詳情
     * @param int $id 文章ID
     * 
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $article = $this->articleService->getArticleById($id);

        if (!$article) {
            return ResponseMaker::error('文章不存在', 404);
        }

        return ResponseMaker::success($this->articleTransformer->transform($article));
    }
}