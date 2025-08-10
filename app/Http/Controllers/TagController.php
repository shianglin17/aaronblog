<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Services\TagService;
use App\Transformer\TagTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagController extends Controller
{
    /**
     * @param TagTransformer $transformer
     * @param TagService $tagService
     */
    public function __construct(
        protected readonly TagTransformer $transformer,
        protected readonly TagService $tagService
    ) {
    }

    /**
     * 獲取所有標籤
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags();
        
        return ApiResponse::ok(
            $this->transformer->transformCollection($tags)
        );
    }

    /**
     * 獲取指定標籤
     * 
     * @param int $id 標籤ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->getTagById($id);
        
        return ApiResponse::ok(
            $this->transformer->transform($tag)
        );
    }

    /**
     * 創建標籤
     * 
     * @param CreateTagRequest $request
     * @return JsonResponse
     */
    public function store(CreateTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tag = $this->tagService->createTag($data);
        
        return ApiResponse::created(
            $this->transformer->transform($tag)
        );
    }

    /**
     * 更新標籤
     * 
     * @param int $id 標籤ID
     * @param UpdateTagRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(int $id, UpdateTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tag = $this->tagService->updateTag($id, $data);
        
        return ApiResponse::ok(
            $this->transformer->transform($tag)
        );
    }

    /**
     * 刪除標籤
     * 
     * @param int $id 標籤ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->tagService->deleteTag($id);
        
        return ApiResponse::ok();
    }
} 