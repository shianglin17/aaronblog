<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Http\Response\ResponseMaker;

class ArticleController extends Controller
{
    protected readonly ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
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

        return ResponseMaker::paginator($articles);
    }
}