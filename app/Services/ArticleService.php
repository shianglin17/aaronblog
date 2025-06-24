<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Cache\ArticleCacheService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    /**
     * @param ArticleRepository $repository
     * @param ArticleCacheService $cacheService
     */
    public function __construct(
        protected readonly ArticleRepository $repository,
        protected readonly ArticleCacheService $cacheService
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
        $this->syncArticleTags($article, $data);

        // 清除相關快取
        $this->cacheService->clearListCache();

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
        $this->syncArticleTags($article, $data);

        // 清除相關快取
        $this->cacheService->clearResourceAllCache($id);

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

        // 清除相關快取
        $this->cacheService->clearResourceAllCache($id);

        return $result;
    }

    /**
     * 同步文章標籤關聯
     *
     * @param Article $article
     * @param array $data
     * @return void
     */
    private function syncArticleTags(Article $article, array $data): void
    {
        if (isset($data['tag_ids'])) {
            $article->tags()->sync($data['tag_ids']);
        }
    }
} 