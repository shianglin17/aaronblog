<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository
{
    /**
     * 獲取所有文章
     *
     * @param array $param 查詢參數
     * @return LengthAwarePaginator
     */
    public function getArticles(Array $param): LengthAwarePaginator
    {
        return Article::select([
            'id', 'title', 'content', 'user_id', 'category_id', 'created_at', 'updated_at'
        ])
        ->with([
            'author:id,name',
            'category:id,name',
            'tags:id,name,slug'
        ])
        ->when(!empty($param['search']), function($query) use ($param) {
            return $query->where('title', 'like', "%{$param['search']}%")
                         ->orWhere('content', 'like', "%{$param['search']}%");
        })
        ->orderBy($param['sort_by'], $param['sort_direction'])
        ->paginate(page: $param['page'], perPage: $param['per_page']);
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
            'id', 'title', 'content', 'user_id', 'category_id', 'created_at', 'updated_at'
        ])
        ->with([
            'author:id,name',
            'category:id,name',
            'tags:id,name,slug'
        ])
        ->find($id);
    }
}