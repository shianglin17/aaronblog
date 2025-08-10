<?php

namespace App\Transformer;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleTransformer extends BaseTransformer
{
    /**
     * 轉換單篇文章資料
     *
     * @param Article $article
     * @return array
     */
    public function transform($article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'description' => $article->description,
            'content' => $article->content,
            'status' => $article->status,
            'author' => [
                'id' => $article->user_id,
                'name' => $article->author->name
            ],
            'category' => [
                'id' => $article->category_id,
                'name' => $article->category->name,
                'slug' => $article->category->slug
            ],
            'tags' => $this->transformTags($article->tags),
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ];
    }

    /**
     * 轉換標籤集合
     *
     * @param Collection $tags
     * @return array
     */
    protected function transformTags(Collection $tags): array
    {
        if ($tags->isEmpty()) {
            return [];
        }

        return $tags->map(function ($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ];
        })->toArray();
    }

} 