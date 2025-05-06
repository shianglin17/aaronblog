<?php

namespace App\Transformer;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleTransformer
{
    /**
     * 轉換單篇文章資料
     *
     * @param Article $article
     * @return array
     */
    public function transform(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'content' => $article->content,
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
            'user_id' => $article->user_id,
            'user_name' => $article->author->name,
            'category_id' => $article->category_id,
            'category_name' => $article->category->name,
            'tags' => $this->transformTags($article->tags),
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

    /**
     * 轉換多篇文章資料
     *
     * @param Collection $articles
     * @return array
     */
    public function transformCollection(Collection $articles): array
    {
        return $articles->map(function ($article) {
            return $this->transform($article);
        })->toArray();
    }
} 