<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Article\ListArticlesRequest;
use App\Http\Requests\Article\GetArticleRequest;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
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
    public function index(ListArticlesRequest $request): JsonResponse
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

        return ResponseMaker::success(
            data: $this->articleTransformer->transform($article),
            message: '文章詳情'
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
        $article = $this->articleService->createArticle($data);

        return ResponseMaker::success(
            $this->articleTransformer->transform($article),
            message: '文章創建成功',
            code: 201
        );
    }

    /**
     * 更新文章
     * @param int $id 文章ID
     * @param UpdateArticleRequest $request
     * 
     * @return JsonResponse
     */
    public function update(int $id, UpdateArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $article = $this->articleService->updateArticle($id, $data);

        if (!$article) {
            return ResponseMaker::error('文章不存在', 404);
        }

        return ResponseMaker::success(
            data: $this->articleTransformer->transform($article),
            message: '文章更新成功'
        );
    }

    /**
     * 刪除文章
     * @param int $id 文章ID
     * 
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->articleService->deleteArticle($id);

        if (!$result) {
            return ResponseMaker::error('文章不存在', 404);
        }

        return ResponseMaker::success(message: '文章刪除成功');
    }
}