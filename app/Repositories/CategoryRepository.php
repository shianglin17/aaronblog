<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * 獲取所有分類
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return Category::all();
    }

    /**
     * 獲取指定ID的分類
     *
     * @param int $id 分類ID
     * @return Category|null
     */
    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * 根據 slug 獲取分類
     *
     * @param string $slug 分類slug
     * @return Category|null
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * 創建新分類
     *
     * @param array $data 分類數據
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * 更新分類
     *
     * @param Category $category 分類模型
     * @param array $data 更新數據
     * @return bool
     */
    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * 刪除分類
     *
     * @param Category $category 分類模型
     * @return bool
     */
    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }
} 