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
     * 取得當前用戶的文章列表
     * @param AdminListArticlesRequest $request
     * 
     * @return JsonResponse
     */
    public function index(AdminListArticlesRequest $request): JsonResponse
    {
        $params = $this->withCurrentUserParams($request->validatedWithDefaults());
        $articles = $this->adminArticleService->getUserArticles($params);

        return ApiResponse::paginated($articles, $this->articleTransformer);
    }

    /**
     * 創建文章
     * @param CreateArticleRequest $request
     * 
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
     * 更新當前用戶的文章
     * @param int $id 文章ID
     * @param UpdateArticleRequest $request
     * 
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
     * 刪除當前用戶的文章
     * @param int $id 文章ID
     * 
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->adminArticleService->deleteUserArticle($id, $this->getCurrentUserId());

        return ApiResponse::ok();
    }
}