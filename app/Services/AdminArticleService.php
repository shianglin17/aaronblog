<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\AdminArticleRepository;
use App\Services\Cache\AdminArticleCacheService;
use App\Services\Cache\ArticleCacheService;
use App\Services\Cache\TagCacheService;
use App\Services\Cache\CategoryCacheService;
use App\Services\AuthorizationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Auth\Access\AuthorizationException;

class AdminArticleService
{
    /**
     * @param AdminArticleRepository $repository
     * @param AdminArticleCacheService $cacheService
     * @param ArticleCacheService $frontendCacheService
     * @param TagCacheService $tagCacheService
     * @param CategoryCacheService $categoryCacheService
     * @param AuthorizationService $authorizationService
     */
    public function __construct(
        protected readonly AdminArticleRepository $repository,
        protected readonly AdminArticleCacheService $cacheService,
        protected readonly ArticleCacheService $frontendCacheService,
        protected readonly TagCacheService $tagCacheService,
        protected readonly CategoryCacheService $categoryCacheService,
        protected readonly AuthorizationService $authorizationService
    ) {
    }

    /**
     * 獲取指定用戶的文章列表
     *
     * @param array $params 查詢參數（包含 user_id）
     * @return LengthAwarePaginator
     */
    public function getUserArticles(array $params): LengthAwarePaginator
    {
        return $this->cacheService->cacheUserArticleList(
            fn() => $this->repository->getUserArticles($params),
            $params
        );
    }
    
    /**
     * 創建用戶文章
     *
     * @param array $data 文章資料
     * @return Article
     */
    public function createUserArticle(array $data): Article
    {
        $article = $this->repository->create($data);

        // 同步標籤關聯
        $article->syncTagsFromData($data);

        // 清除相關快取
        $this->clearRelatedCache($article->id, $data['user_id'] ?? null);

        return $article;
    }

    /**
     * 更新用戶文章（檢查所有權）
     *
     * @param int $id 文章ID
     * @param array $data 文章資料
     * @param int $userId 用戶ID
     * @return Article
     * @throws ModelNotFoundException|AccessDeniedHttpException
     */
    public function updateUserArticle(int $id, array $data, int $userId): Article
    {
        $article = $this->repository->getById($id);
        
        // 檢查文章所有權
        if (!$this->authorizationService->canModifyArticle($article, $userId)) {
            throw new AuthorizationException('您沒有權限修改此文章');
        }

        $this->repository->update($article, $data);

        // 同步標籤關聯
        $article->syncTagsFromData($data);

        // 清除相關快取
        $this->clearRelatedCache($id, $userId);

        return $article->fresh();
    }

    /**
     * 刪除用戶文章（檢查所有權）
     *
     * @param int $id 文章ID
     * @param int $userId 用戶ID
     * @return bool
     * @throws ModelNotFoundException|AccessDeniedHttpException
     */
    public function deleteUserArticle(int $id, int $userId): bool
    {
        $article = $this->repository->getById($id);
        
        // 檢查文章所有權
        if (!$this->authorizationService->canDeleteArticle($article, $userId)) {
            throw new AuthorizationException('您沒有權限刪除此文章');
        }

        $result = $this->repository->delete($article);

        // 清除相關快取
        $this->clearRelatedCache($id, $userId);

        return $result;
    }


    /**
     * 清除相關快取
     *
     * @param int $articleId
     * @param int|null $userId
     * @return void
     */
    private function clearRelatedCache(int $articleId, ?int $userId = null): void
    {
        // 清除用戶文章相關快取（後台）
        $this->cacheService->clearUserArticleCache($articleId, $userId);
        
        // 清除前台文章快取
        $this->frontendCacheService->clearListCache();
        $this->frontendCacheService->clearDetailCache($articleId);
        
        // 清除標籤和分類快取（因為文章數量可能改變）
        $this->tagCacheService->clearAllCache();
        $this->categoryCacheService->clearAllCache();
    }
}