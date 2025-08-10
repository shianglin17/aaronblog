<?php

namespace App\Transformer;

use App\Models\Category;

class CategoryTransformer extends BaseTransformer
{
    /**
     * 轉換單個分類對象
     *
     * @param Category $category
     * @return array
     */
    public function transform($category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'articles_count' => $category->articles_count,
            'created_at' => $category->created_at
        ];
    }

} 