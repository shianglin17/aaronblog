<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @template T of Model
 */
abstract class BaseRepository
{
    protected Model $model;

    /**
     * 獲取所有資源
     *
     * @param string|null $countRelation 要計數的關聯名稱
     * @return T[]|Collection<T>
     */
    public function getAll(?string $countRelation = null): Collection
    {
        $query = $this->model->newQuery();
        
        if ($countRelation) {
            $this->addRelationCount($query, $countRelation);
        }
        
        return $query->get();
    }

    /**
     * 根據 ID 獲取資源
     *
     * @param int $id
     * @param string|null $countRelation 要計數的關聯名稱
     * @return T
     * @throws ModelNotFoundException
     */
    public function getById(int $id, ?string $countRelation = null): Model
    {
        $query = $this->model->newQuery();
        
        if ($countRelation) {
            $this->addRelationCount($query, $countRelation);
        }
        
        return $query->findOrFail($id);
    }

    /**
     * 創建資源
     *
     * @param array $data
     * @param string|null $countRelation 要計數的關聯名稱
     * @return T
     */
    public function create(array $data, ?string $countRelation = null): Model
    {
        $model = $this->model->create($data);
        
        if ($countRelation) {
            $this->loadRelationCount($model, $countRelation);
        }
        
        return $model;
    }

    /**
     * 更新資源
     *
     * @param Model $model
     * @param array $data
     * @param string|null $countRelation 要計數的關聯名稱
     * @return Model
     */
    public function update(Model $model, array $data, ?string $countRelation = null): Model
    {
        $model->update($data);
        
        if ($countRelation) {
            $this->loadRelationCount($model, $countRelation);
        }
        
        return $model;
    }

    /**
     * 刪除資源
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * 為查詢添加關聯計數
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $countRelation
     * @return void
     */
    private function addRelationCount($query, string $countRelation): void
    {
        $relationMethod = $this->mapRelationName($countRelation);
        
        // 檢查模型是否有此關聯方法
        if (method_exists($this->model, $relationMethod)) {
            $query->withCount([$relationMethod . ' as articles_count']);
        }
    }

    /**
     * 為模型載入關聯計數
     *
     * @param Model $model
     * @param string $countRelation
     * @return void
     */
    private function loadRelationCount(Model $model, string $countRelation): void
    {
        $relationMethod = $this->mapRelationName($countRelation);
        
        if (method_exists($model, $relationMethod)) {
            $model->loadCount([$relationMethod . ' as articles_count']);
        }
    }

    /**
     * 映射關聯名稱到實際方法名
     *
     * @param string $countRelation
     * @return string
     */
    private function mapRelationName(string $countRelation): string
    {
        return match ($countRelation) {
            'published_articles' => 'publishedArticles',
            default => $countRelation
        };
    }
} 