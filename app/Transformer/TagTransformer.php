<?php

namespace App\Transformer;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagTransformer
{
    /**
     * 轉換單個標籤對象
     *
     * @param Tag $tag
     * @return array
     */
    public function transform(Tag $tag): array
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug
        ];
    }

    /**
     * 轉換標籤集合
     *
     * @param Collection $tags
     * @return array
     */
    public function transformCollection(Collection $tags): array
    {
        return $tags->map(function ($tag) {
            return $this->transform($tag);
        })->toArray();
    }
} 