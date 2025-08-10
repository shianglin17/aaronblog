<?php

namespace App\Transformer;

use App\Models\Tag;

class TagTransformer extends BaseTransformer
{
    /**
     * 轉換單個標籤對象
     *
     * @param Tag $tag
     * @return array
     */
    public function transform($tag): array
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'articles_count' => $tag->articles_count,
            'created_at' => $tag->created_at
        ];
    }

} 