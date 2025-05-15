<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $repository;

    /**
     * @param CategoryRepository $repository
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 獲取所有分類
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return $this->repository->getAllCategories();
    }

    /**
     * 獲取指定ID的分類
     *
     * @param int $id 分類ID
     * @return Category|null
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->repository->getCategoryById($id);
    }

    /**
     * 創建新分類
     *
     * @param array $data 分類數據
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        // 在這裡不需要自動生成 slug，因為 slug 已設為必填
        // 但如果需要進行其他處理，可以在這裡添加

        return $this->repository->createCategory($data);
    }

    /**
     * 更新分類
     *
     * @param int $id 分類ID
     * @param array $data 更新數據
     * @return Category|null
     */
    public function updateCategory(int $id, array $data): ?Category
    {
        $category = $this->getCategoryById($id);
        if (!$category) {
            return null;
        }

        $this->repository->updateCategory($category, $data);
        return $category->refresh();
    }

    /**
     * 刪除分類
     *
     * @param int $id 分類ID
     * @return bool
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);
        if (!$category) {
            return false;
        }

        // 檢查是否有關聯的文章
        if ($category->articles()->count() > 0) {
            // 可以選擇拒絕刪除，或是將關聯文章的分類設為 null
            // 這裡選擇將關聯文章的分類設為 null
            $category->articles()->update(['category_id' => null]);
        }
        
        return $this->repository->deleteCategory($category);
    }
} 