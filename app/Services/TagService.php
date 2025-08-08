<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Cache\TagCacheService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * @param TagRepository $repository
     * @param TagCacheService $cacheService
     */
    public function __construct(
        protected readonly TagRepository $repository,
        protected readonly TagCacheService $cacheService
    ) {
    }

    /**
     * 獲取所有標籤（含快取）
     *
     * @return Collection
     */
    public function getAllTags(): Collection
    {
        return $this->cacheService->cacheList(
            fn() => $this->repository->getAll()
        );
    }

    /**
     * 根據ID獲取標籤（含快取）
     *
     * @param int $id
     * @return Tag
     * @throws ModelNotFoundException
     */
    public function getTagById(int $id): Tag
    {
        return $this->cacheService->cacheDetail(
            $id,
            fn() => $this->repository->getById($id)
        );
    }

    /**
     * 創建新標籤
     *
     * @param array $data
     * @return Tag
     */
    public function createTag(array $data): Tag
    {
        $tag = $this->repository->create($data, 'articles');

        $this->cacheService->clearListCache();

        return $tag;
    }

    /**
     * 更新標籤
     *
     * @param int $id
     * @param array $data
     * @return Tag
     * @throws ModelNotFoundException
     */
    public function updateTag(int $id, array $data): Tag
    {
        $tag = $this->getTagById($id);

        $this->repository->update($tag, $data, 'articles');

        $this->cacheService->clearResourceAllCache($id);

        return $tag->fresh();
    }

    /**
     * 刪除標籤
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteTag(int $id): bool
    {
        $tag = $this->getTagById($id);

        // 標籤採用寬鬆模式：允許刪除有關聯文章的標籤
        // 多對多關聯會自動清理中間表記錄
        $result = $this->repository->delete($tag);

        $this->cacheService->clearResourceAllCache($id);

        return $result;
    }
} 