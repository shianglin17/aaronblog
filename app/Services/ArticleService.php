<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
} 