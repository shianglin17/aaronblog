<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    protected ArticleRepository $repository;

    /**
     * @param ArticleRepository $repository
     */
    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 獲取所有文章列表
     *
     * @return LengthAwarePaginator
     */
    public function getArticles(Array $param): LengthAwarePaginator
    {
        return $this->repository->getArticles($param);
    }
    
    /**
     * 獲取單篇文章詳情
     *
     * @param int $id 文章ID
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article
    {
        return $this->repository->getArticleById($id);
    }

    /**
     * 創建新文章
     *
     * @param array $data 文章資料
     * @return Article
     */
    public function createArticle(array $data): Article
    {
        // 如果未設定狀態，預設為草稿
        if (!isset($data['status'])) {
            $data['status'] = Article::STATUS_DRAFT;
        }

        // 設定當前登入用戶為作者
        if (!isset($data['user_id']) && Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        // 創建文章
        $article = $this->repository->createArticle($data);

        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }

        return $article;
    }

    /**
     * 更新文章
     *
     * @param int $id 文章ID
     * @param array $data 更新資料
     * @return Article|null
     */
    public function updateArticle(int $id, array $data): ?Article
    {
        $article = $this->getArticleById($id);
        if (!$article) {
            return null;
        }

        // 更新文章
        $this->repository->updateArticle($article, $data);

        // 處理標籤
        if (isset($data['tags']) && is_array($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }

        return $article->refresh();
    }

    /**
     * 刪除文章
     *
     * @param int $id 文章ID
     * @return bool
     */
    public function deleteArticle(int $id): bool
    {
        $article = $this->getArticleById($id);
        if (!$article) {
            return false;
        }

        // 清除標籤關聯
        $article->tags()->detach();

        // 軟刪除文章
        return $this->repository->deleteArticle($article);
    }

    /**
     * 發佈文章
     *
     * @param int $id 文章ID
     * @return Article|null
     */
    public function publishArticle(int $id): ?Article
    {
        $article = $this->getArticleById($id);
        if (!$article) {
            return null;
        }

        return $this->updateArticle($id, ['status' => Article::STATUS_PUBLISHED]);
    }

    /**
     * 將文章設為草稿
     *
     * @param int $id 文章ID
     * @return Article|null
     */
    public function draftArticle(int $id): ?Article
    {
        $article = $this->getArticleById($id);
        if (!$article) {
            return null;
        }

        return $this->updateArticle($id, ['status' => Article::STATUS_DRAFT]);
    }
} 