<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Cache\TagCacheService;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * @var TagRepository
     */
    protected TagRepository $repository;

    /**
     * @var TagCacheService
     */
    protected TagCacheService $cacheService;

    /**
     * @param TagRepository $repository
     * @param TagCacheService $cacheService
     */
    public function __construct(
        TagRepository $repository,
        TagCacheService $cacheService
    ) {
        $this->repository = $repository;
        $this->cacheService = $cacheService;
    }

    /**
     * 獲取所有標籤（含快取）
     *
     * @return Collection
     */
    public function getAllTags(): Collection
    {
        return $this->cacheService->cacheTagList(
            fn() => $this->repository->getAllTags()
        );
    }

    /**
     * 獲取指定ID的標籤（含快取）
     *
     * @param int $id 標籤ID
     * @return Tag|null
     */
    public function getTagById(int $id): ?Tag
    {
        return $this->cacheService->cacheTagDetail(
            $id,
            fn() => $this->repository->getTagById($id)
        );
    }

    /**
     * 創建新標籤
     *
     * @param array $data 標籤數據
     * @return Tag
     */
    public function createTag(array $data): Tag
    {
        $tag = $this->repository->createTag($data);
        
        // 清除標籤列表快取
        $this->cacheService->clearListCache();
        
        return $tag;
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
        
        // 清除該標籤的所有相關快取
        $this->cacheService->clearTagAllCache($id);
        
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
        
        $result = $this->repository->deleteTag($tag);
        
        // 刪除成功後清除相關快取
        if ($result) {
            $this->cacheService->clearTagAllCache($id);
        }
        
        return $result;
    }
} 