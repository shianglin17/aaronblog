<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * @param CategoryRepository $repository
     * @param CategoryCacheService $cacheService
     */
    public function __construct(
        protected readonly CategoryRepository $repository,
        protected readonly CategoryCacheService $cacheService
    ) {
    }

    /**
     * 獲取所有分類（含快取）
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return $this->cacheService->cacheList(
            fn() => $this->repository->getAll()
        );
    }

    /**
     * 根據ID獲取分類（含快取）
     *
     * @param int $id
     * @return Category
     * @throws ModelNotFoundException
     */
    public function getCategoryById(int $id): Category
    {
        return $this->cacheService->cacheDetail(
            $id,
            fn() => $this->repository->getById($id)
        );
    }

    /**
     * 創建新分類
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        $category = $this->repository->create($data);

        $this->cacheService->clearListCache();

        return $category;
    }

    /**
     * 更新分類
     *
     * @param int $id
     * @param array $data
     * @return Category
     * @throws ModelNotFoundException
     */
    public function updateCategory(int $id, array $data): Category
    {
        $category = $this->getCategoryById($id);

        $this->repository->update($category, $data);

        $this->cacheService->clearResourceAllCache($id);

        return $category->fresh();
    }

    /**
     * 刪除分類
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);

        $result = $this->repository->delete($category);

        $this->cacheService->clearResourceAllCache($id);

        return $result;
    }
} 