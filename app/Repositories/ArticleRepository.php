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
     * @return LengthAwarePaginator
     */
    public function getArticles(Array $param): LengthAwarePaginator
    {
        $articles = Article::query()
            ->selectRaw('user_id, category_id, title, content, created_at')
            ->orderBy($param['sort_by'], $param['sort_direction'])
            ->paginate($param['page'], $param['per_page']);

        return $articles;
    }
}