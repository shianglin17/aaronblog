<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * @var TagRepository
     */
    protected TagRepository $repository;

    /**
     * @param TagRepository $repository
     */
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 獲取所有標籤
     *
     * @return Collection
     */
    public function getAllTags(): Collection
    {
        return $this->repository->getAllTags();
    }

    /**
     * 獲取指定ID的標籤
     *
     * @param int $id 標籤ID
     * @return Tag|null
     */
    public function getTagById(int $id): ?Tag
    {
        return $this->repository->getTagById($id);
    }

    /**
     * 創建新標籤
     *
     * @param array $data 標籤數據
     * @return Tag
     */
    public function createTag(array $data): Tag
    {
        return $this->repository->createTag($data);
    }

    /**
     * 更新標籤
     *
     * @param int $id 標籤ID
     * @param array $data 更新數據
     * @return Tag|null
     */
    public function updateTag(int $id, array $data): ?Tag
    {
        $tag = $this->getTagById($id);
        if (!$tag) {
            return null;
        }

        $this->repository->updateTag($tag, $data);
        return $tag->refresh();
    }

    /**
     * 刪除標籤
     *
     * @param int $id 標籤ID
     * @return bool
     */
    public function deleteTag(int $id): bool
    {
        $tag = $this->getTagById($id);
        if (!$tag) {
            return false;
        }

        // 刪除標籤前先解除與文章的關聯
        $tag->articles()->detach();
        
        return $this->repository->deleteTag($tag);
    }
} 