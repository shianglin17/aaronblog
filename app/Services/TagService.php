<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Cache\TagCacheService;
use App\Exceptions\ResourceNotFoundException;
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
        return $this->cacheService->cacheTagList(
            fn() => $this->repository->getAll()
        );
    }

    /**
     * 根據ID獲取標籤（含快取）
     *
     * @param int $id
     * @return Tag
     * @throws ResourceNotFoundException
     */
    public function getTagById(int $id): Tag
    {
        $tag = $this->cacheService->cacheTagDetail(
            $id,
            fn() => $this->repository->getById($id)
        );
        
        if ($tag === null) {
            throw new ResourceNotFoundException('標籤', $id);
        }
        
        return $tag;
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
     */
    public function updateTag(int $id, array $data): Tag
    {
        $tag = $this->getTagById($id);

        $this->repository->update($tag, $data);

        $this->cacheService->clearTagAllCache($id);

        return $tag->fresh();
    }

    /**
     * 刪除標籤
     *
     * @param int $id
     * @return bool
     */
    public function deleteTag(int $id): bool
    {
        $tag = $this->getTagById($id);

        $result = $this->repository->delete($tag);

        if ($result) {
            $this->cacheService->clearTagAllCache($id);
        }

        return $result;
    }
} 