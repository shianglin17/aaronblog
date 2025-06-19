<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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
        return $this->model->all();
    }

    /**
     * 根據 ID 獲取資源
     *
     * @param int $id
     * @return T|null
     */
    public function getById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * 創建資源
     *
     * @param array $data
     * @return T
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * 更新資源
     *
     * @param Model $model
     * @param array $data
     * @return bool
     */
    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
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