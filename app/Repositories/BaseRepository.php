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
     * @return T[]|Collection<T>
     */
    public function getAll(): Collection
    {
        return $this->model->newQuery()->withCount('articles')->get();
    }

    /**
     * 根據 ID 獲取資源
     *
     * @param int $id
     * @return T
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Model
    {
        return $this->model->newQuery()->withCount('articles')->findOrFail($id);
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
        
        if ($countRelation && method_exists($model, $countRelation)) {
            $model->loadCount($countRelation);
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
        
        if ($countRelation && method_exists($model, $countRelation)) {
            $model->loadCount($countRelation);
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
} 