<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository
{
    /**
     * 獲取所有文章
     *
     * @param array $params 查詢參數
     * @return LengthAwarePaginator
     */
    public function getArticles(array $params): LengthAwarePaginator
    {
        return Article::select([
            'id', 'title', 'slug', 'description', 'content', 'status', 'user_id', 'category_id', 'created_at', 'updated_at'
        ])
        ->with([
            'author:id,name',
            'category:id,name,slug',
            'tags:id,name,slug'
        ])
        ->when(!empty($params['search']), fn(Builder $query): Builder => 
            $query->where('title', 'like', "%{$params['search']}%")
                  ->orWhere('content', 'like', "%{$params['search']}%")
                  ->orWhere('description', 'like', "%{$params['search']}%")
        )
        ->when($params['status'] !== 'all', fn(Builder $query): Builder => 
            $query->where('status', $params['status'])
        )
        ->when(!empty($params['category']), fn(Builder $query): Builder => 
            $query->whereHas('category', fn(Builder $q): Builder => 
                $q->where('slug', $params['category'])
            )
        )
        ->when(!empty($params['tags']), fn(Builder $query): Builder => 
            $query->whereHas('tags', fn(Builder $q): Builder => 
                $q->whereIn('slug', $params['tags'])
            )
        )
        ->orderBy($params['sort_by'], $params['sort_direction'])
        ->paginate(
            perPage: $params['per_page'],
            page: $params['page']
        );
    }

    /**
     * 獲取單篇文章
     *
     * @param int $id 文章ID
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article
    {
        return Article::select([
            'id', 'title', 'slug', 'description', 'content', 'status', 'user_id', 'category_id', 'created_at', 'updated_at'
        ])
        ->with([
            'author:id,name',
            'category:id,name,slug',
            'tags:id,name,slug'
        ])
        ->find($id);
    }

    /**
     * 創建文章
     *
     * @param array $data 文章資料
     * @return Article
     */
    public function createArticle(array $data): Article
    {
        return Article::create($data);
    }

    /**
     * 更新文章
     *
     * @param Article $article 文章模型
     * @param array $data 更新資料
     * @return bool
     */
    public function updateArticle(Article $article, array $data): bool
    {
        return $article->update($data);
    }

    /**
     * 刪除文章
     *
     * @param Article $article 文章模型
     * @return bool
     */
    public function deleteArticle(Article $article): bool
    {
        return $article->delete();
    }
}