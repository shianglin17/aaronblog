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
     * @param array $param 查詢參數
     * @return LengthAwarePaginator
     */
    public function getArticles(array $param): LengthAwarePaginator
    {
        return Article::select([
            'id', 'title', 'slug', 'description', 'content', 'status', 'user_id', 'category_id', 'created_at', 'updated_at'
        ])
        ->with([
            'author:id,name',
            'category:id,name,slug',
            'tags:id,name,slug'
        ])
        ->when(!empty($param['search']), fn(Builder $query): Builder => 
            $query->where('title', 'like', "%{$param['search']}%")
                  ->orWhere('content', 'like', "%{$param['search']}%")
                  ->orWhere('description', 'like', "%{$param['search']}%")
        )
        ->when($param['status'] !== 'all', fn(Builder $query): Builder => 
            $query->where('status', $param['status'])
        )
        // 依照分類篩選（使用分類的 slug）
        ->when(!empty($param['category']), fn(Builder $query): Builder => 
            $query->whereHas('category', fn(Builder $q): Builder => 
                $q->where('slug', $param['category'])
            )
        )
        // 依照標籤篩選（使用標籤的 slug，支援多選）
        ->when(!empty($param['tags']), fn(Builder $query): Builder => 
            $query->whereHas('tags', fn(Builder $q): Builder => 
                $q->whereIn('slug', $param['tags'])
            )
        )
        ->orderBy($param['sort_by'] ?? 'created_at', $param['sort_direction'] ?? 'desc')
        ->paginate(
            perPage: $param['per_page'] ?? 15,
            page: $param['page'] ?? 1
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