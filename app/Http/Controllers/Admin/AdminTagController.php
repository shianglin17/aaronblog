<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\ApiResponse;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Services\TagService;
use App\Transformer\TagTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminTagController extends Controller
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
     * 創建標籤（管理後台）
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
     * 更新標籤（管理後台）
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
     * 刪除標籤（管理後台）
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