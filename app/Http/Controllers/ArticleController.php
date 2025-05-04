<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Support\ResponseMaker;

class ArticleController extends Controller
{
    protected readonly ArticleService $article_service;

    public function __construct(ArticleService $article_service)
    {
        $this->article_service = $article_service;
    }

    /**
     * 獲取文章列表
     * @param ListArticlesRequest $request
     * 
     * @return JsonResponse
     */
    public function list(ListArticlesRequest $request): JsonResponse
    {
        $param = $request->validated();
        $articles = $this->article_service->getArticles($param);

        return ResponseMaker::paginator($articles);
    }
}