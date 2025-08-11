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
    const DEFAULT_COLUMNS = [
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
    public function getById(int $id, ?string $countRelation = null): Article
    {
        $query = Article::select(self::DEFAULT_COLUMNS)
            ->with([
                'author:id,name',
                'category:id,name,slug',
                'tags:id,name,slug'
            ]);

        // 遵循父類契約：處理關聯計數參數
        if ($countRelation) {
            $this->addRelationCount($query, $countRelation);
        }

        return $query->findOrFail($id);
    }

    /**
     * 獲取所有文章
     *
     * @param array $params 查詢參數
     * @return LengthAwarePaginator
     */
    public function getArticles(array $params): LengthAwarePaginator
    {
        return $this->buildBaseQuery()
            ->tap(fn($query) => $this->applySearch($query, $params['search'] ?? null))
            ->tap(fn($query) => $this->applyStatusFilter($query, $params['status']))
            ->tap(fn($query) => $this->applyCategoryFilter($query, $params['category'] ?? null))
            ->tap(fn($query) => $this->applyTagsFilter($query, $params['tags'] ?? null))
            ->tap(fn($query) => $this->applySorting($query, $params['sort_by'], $params['sort_direction']))
            ->paginate(perPage: $params['per_page'], page: $params['page']);
    }

    /**
     * 建立基礎查詢
     */
    private function buildBaseQuery(): Builder
    {
        return Article::select(self::DEFAULT_COLUMNS)
            ->with(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug']);
    }

    /**
     * 套用搜尋過濾
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        $query->when(!empty($search), fn(Builder $q) => 
            $q->where(function(Builder $subQuery) use ($search) {
                return $subQuery->where('title', 'like', "%{$search}%")
                               ->orWhere('content', 'like', "%{$search}%")
                               ->orWhere('description', 'like', "%{$search}%");
            })
        );
    }

    /**
     * 套用狀態過濾
     */
    private function applyStatusFilter(Builder $query, string $status): void
    {
        $query->when($status !== 'all', fn(Builder $q) => 
            $q->where('status', $status)
        );
    }

    /**
     * 套用分類過濾
     */
    private function applyCategoryFilter(Builder $query, ?string $categorySlug): void
    {
        $query->when(!empty($categorySlug), fn(Builder $q) => 
            $q->whereHas('category', fn(Builder $subQuery) => 
                $subQuery->where('slug', $categorySlug)
            )
        );
    }

    /**
     * 套用標籤過濾
     */
    private function applyTagsFilter(Builder $query, ?array $tagSlugs): void
    {
        $query->when(!empty($tagSlugs), fn(Builder $q) => 
            $q->whereHas('tags', fn(Builder $subQuery) => 
                $subQuery->whereIn('slug', $tagSlugs)
            )
        );
    }

    /**
     * 套用排序
     */
    private function applySorting(Builder $query, string $sortBy, string $direction): void
    {
        $query->orderBy($sortBy, $direction);
    }
}