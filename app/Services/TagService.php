<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Cache\TagCacheService;
use App\Exceptions\ResourceInUseException;
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
        $tag = $this->repository->create($data);

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

        $this->repository->update($tag, $data);

        $this->cacheService->clearResourceAllCache($id);

        return $tag->fresh();
    }

    /**
     * 刪除標籤
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     * @throws ResourceInUseException
     */
    public function deleteTag(int $id): bool
    {
        $tag = $this->getTagById($id);

        // 檢查是否有文章正在使用此標籤
        if ($tag->articles()->exists()) {
            $usageCount = $tag->articles()->count();
            throw new ResourceInUseException('標籤', $id, '文章', $usageCount);
        }

        $result = $this->repository->delete($tag);

        $this->cacheService->clearResourceAllCache($id);

        return $result;
    }
} 