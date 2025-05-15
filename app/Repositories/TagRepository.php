<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagRepository
{
    /**
     * 獲取所有標籤
     *
     * @return Collection
     */
    public function getAllTags(): Collection
    {
        return Tag::all();
    }

    /**
     * 獲取指定ID的標籤
     *
     * @param int $id 標籤ID
     * @return Tag|null
     */
    public function getTagById(int $id): ?Tag
    {
        return Tag::find($id);
    }

    /**
     * 根據 slug 獲取標籤
     *
     * @param string $slug 標籤slug
     * @return Tag|null
     */
    public function getTagBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    /**
     * 創建新標籤
     *
     * @param array $data 標籤數據
     * @return Tag
     */
    public function createTag(array $data): Tag
    {
        return Tag::create($data);
    }

    /**
     * 更新標籤
     *
     * @param Tag $tag 標籤模型
     * @param array $data 更新數據
     * @return bool
     */
    public function updateTag(Tag $tag, array $data): bool
    {
        return $tag->update($data);
    }

    /**
     * 刪除標籤
     *
     * @param Tag $tag 標籤模型
     * @return bool
     */
    public function deleteTag(Tag $tag): bool
    {
        return $tag->delete();
    }
} 