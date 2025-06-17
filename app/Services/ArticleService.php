<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Cache\ArticleCacheService;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    protected ArticleRepository $repository;

    /**
     * @var ArticleCacheService
     */
    protected ArticleCacheService $cacheService;

    /**
     * @param ArticleRepository $repository
     * @param ArticleCacheService $cacheService
     */
    public function __construct(
        ArticleRepository $repository,
        ArticleCacheService $cacheService
    ) {
        $this->repository = $repository;
        $this->cacheService = $cacheService;
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
            $params,
            fn() => $this->repository->getArticles($params)
        );
    }
    
    /**
     * 獲取單篇文章詳情（含快取）
     *
     * @param int $id 文章ID
     * @return Article
     * @throws ResourceNotFoundException
     */
    public function getArticleById(int $id): Article
    {
        $article = $this->cacheService->cacheArticleDetail(
            $id,
            fn() => $this->repository->getArticleById($id)
        );
        
        if ($article === null) {
            throw new ResourceNotFoundException('文章', $id);
        }
        
        return $article;
    }

    /**
     * 創建新文章
     *
     * @param array $data 文章資料
     * @return Article
     */
    public function createArticle(array $data): Article
    {
        $article = $this->repository->createArticle($data);

        // 同步標籤關聯
        $this->syncArticleTags($article, $data);

        // 清除相關快取
        $this->cacheService->clearAllListCache();

        return $article;
    }

    /**
     * 更新文章
     *
     * @param int $id 文章ID
     * @param array $data 更新資料
     * @return Article
     * @throws ResourceNotFoundException
     */
    public function updateArticle(int $id, array $data): Article
    {
        $article = $this->getArticleById($id);

        // 更新文章
        $this->repository->updateArticle($article, $data);

        // 同步標籤關聯
        $this->syncArticleTags($article, $data);

        // 清除該文章的所有相關快取
        $this->cacheService->clearArticleAllCache($id);

        return $article->refresh();
    }

    /**
     * 同步文章標籤關聯
     *
     * @param Article $article 文章模型
     * @param array $data 資料陣列
     * @return void
     */
    private function syncArticleTags(Article $article, array $data): void
    {
        if (isset($data['tags']) && is_array($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }
    }

    /**
     * 刪除文章
     *
     * @param int $id 文章ID
     * @return bool
     * @throws ResourceNotFoundException
     */
    public function deleteArticle(int $id): bool
    {
        $article = $this->getArticleById($id);

        // 清除標籤關聯
        $article->tags()->detach();

        // 軟刪除文章
        $result = $this->repository->deleteArticle($article);

        // 刪除成功後清除相關快取
        if ($result) {
            $this->cacheService->clearArticleAllCache($id);
        }

        return $result;
    }
} 