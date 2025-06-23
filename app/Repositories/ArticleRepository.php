<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Article>
 */
class ArticleRepository extends BaseRepository
{
    const DEFAULT_CONLUMNS = [
        'id', 'title', 'slug', 'description', 'content', 'status', 'user_id', 'category_id', 'created_at', 'updated_at'
    ];

    public function __construct()
    {
        $this->model = new Article();
    }

    /**
     * Override 父類別方法，提供優化的查詢邏輯
     *
     * @param int $id 文章ID
     * @return Article
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Article
    {
        return Article::select(self::DEFAULT_CONLUMNS)
        ->with([
            'author:id,name',
            'category:id,name,slug',
            'tags:id,name,slug'
        ])
        ->findOrFail($id);
    }

    /**
     * 獲取所有文章
     *
     * @param array $params 查詢參數
     * @return LengthAwarePaginator
     */
    public function getArticles(array $params): LengthAwarePaginator
    {
        return Article::select(self::DEFAULT_CONLUMNS)
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
}