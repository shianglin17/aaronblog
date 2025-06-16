<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\Cache\CategoryCacheService;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $repository;

    /**
     * @var CategoryCacheService
     */
    protected CategoryCacheService $cacheService;

    /**
     * @param CategoryRepository $repository
     * @param CategoryCacheService $cacheService
     */
    public function __construct(
        CategoryRepository $repository,
        CategoryCacheService $cacheService
    ) {
        $this->repository = $repository;
        $this->cacheService = $cacheService;
    }

    /**
     * 獲取所有分類（含快取）
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return $this->cacheService->cacheCategoryList(
            fn() => $this->repository->getAllCategories()
        );
    }

    /**
     * 獲取指定ID的分類（含快取）
     *
     * @param int $id 分類ID
     * @return Category
     * @throws ResourceNotFoundException
     */
    public function getCategoryById(int $id): Category
    {
        $category = $this->cacheService->cacheCategoryDetail(
            $id,
            fn() => $this->repository->getCategoryById($id)
        );
        
        if ($category === null) {
            throw new ResourceNotFoundException('分類', $id);
        }
        
        return $category;
    }

    /**
     * 創建新分類
     *
     * @param array $data 分類數據
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        $category = $this->repository->createCategory($data);
        
        // 清除分類列表快取
        $this->cacheService->clearListCache();
        
        return $category;
    }

    /**
     * 更新分類
     *
     * @param int $id 分類ID
     * @param array $data 更新數據
     * @return Category
     * @throws ResourceNotFoundException
     */
    public function updateCategory(int $id, array $data): Category
    {
        $category = $this->getCategoryById($id);

        $this->repository->updateCategory($category, $data);
        
        // 清除該分類的所有相關快取
        $this->cacheService->clearCategoryAllCache($id);
        
        return $category->refresh();
    }

    /**
     * 刪除分類
     *
     * @param int $id 分類ID
     * @return bool
     * @throws ResourceNotFoundException
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);

        // 檢查是否有關聯的文章
        if ($category->articles()->count() > 0) {
            // 可以選擇拒絕刪除，或是將關聯文章的分類設為 null
            // 這裡選擇將關聯文章的分類設為 null
            $category->articles()->update(['category_id' => null]);
        }
        
        $result = $this->repository->deleteCategory($category);
        
        // 刪除成功後清除相關快取
        if ($result) {
            $this->cacheService->clearCategoryAllCache($id);
        }
        
        return $result;
    }
} 