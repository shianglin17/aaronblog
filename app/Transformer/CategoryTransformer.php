<?php

namespace App\Transformer;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryTransformer
{
    /**
     * 轉換單個分類對象
     *
     * @param Category $category
     * @return array
     */
    public function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'created_at' => $category->created_at
        ];
    }

    /**
     * 轉換分類集合
     *
     * @param Collection $categories
     * @return array
     */
    public function transformCollection(Collection $categories): array
    {
        return $categories->map(function ($category) {
            return $this->transform($category);
        })->toArray();
    }
} 