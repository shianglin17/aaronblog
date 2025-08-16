<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Cache\ArticleCacheService;
use App\Services\Cache\TagCacheService;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    /**
     * @param ArticleRepository $repository
     * @param ArticleCacheService $cacheService
     * @param TagCacheService $tagCacheService
     * @param CategoryCacheService $categoryCacheService
     */
    public function __construct(
        protected readonly ArticleRepository $repository,
        protected readonly ArticleCacheService $cacheService,
        protected readonly TagCacheService $tagCacheService,
        protected readonly CategoryCacheService $categoryCacheService
    ) {
    }

    /**
     * 獲取所有文章列表（含快取）
     *
     * @param array $params 查詢參數
     * @return LengthAwarePaginator
     */
    public function getArticles(array $params): LengthAwarePaginator
    {
        // 使用快取服務
        return $this->cacheService->cacheArticleList(
            fn() => $this->repository->getArticles($params),
            $params
        );
    }
    
    /**
     * 獲取單篇文章詳情（含快取）
     *
     * @param int $id 文章ID
     * @return Article
     * @throws ModelNotFoundException
     */
    public function getArticleById(int $id): Article
    {
        return $this->cacheService->cacheDetail(
            $id,
            fn() => $this->repository->getById($id)
        );
    }

    /**
     * 創建新文章
     *
     * @param array $data 文章資料
     * @return Article
     */
    public function createArticle(array $data): Article
    {
        $article = $this->repository->create($data);

        // 同步標籤關聯
        $article->syncTagsFromData($data);

        // 清除文章相關快取
        $this->cacheService->clearListCache();
        
        // 清除標籤和分類快取（因為文章數量可能改變）
        $this->tagCacheService->clearAllCache();
        $this->categoryCacheService->clearAllCache();

        return $article;
    }

    /**
     * 更新文章
     *
     * @param int $id 文章ID
     * @param array $data 文章資料
     * @return Article
     * @throws ModelNotFoundException
     */
    public function updateArticle(int $id, array $data): Article
    {
        $article = $this->getArticleById($id);

        $this->repository->update($article, $data);

        // 同步標籤關聯
        $article->syncTagsFromData($data);

        // 清除文章相關快取
        $this->cacheService->clearResourceAllCache($id);
        
        // 清除標籤和分類快取（因為標籤關聯可能改變）
        $this->tagCacheService->clearAllCache();
        $this->categoryCacheService->clearAllCache();

        return $article->fresh();
    }

    /**
     * 刪除文章
     *
     * @param int $id 文章ID
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteArticle(int $id): bool
    {
        $article = $this->getArticleById($id);

        $result = $this->repository->delete($article);

        // 清除文章相關快取
        $this->cacheService->clearResourceAllCache($id);
        
        // 清除標籤快取（因為文章數量改變）
        $this->tagCacheService->clearAllCache();
        
        // 清除分類快取（因為文章數量改變）
        $this->categoryCacheService->clearAllCache();

        return $result;
    }

} 