<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Response\ResponseMaker;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Services\TagService;
use App\Transformer\TagTransformer;
use Illuminate\Http\JsonResponse;
use App\Exceptions\ResourceNotFoundException;

class TagController extends Controller
{
    protected readonly TagTransformer $transformer;
    protected readonly TagService $tagService;

    public function __construct(TagTransformer $transformer, TagService $tagService)
    {
        $this->transformer = $transformer;
        $this->tagService = $tagService;
    }

    /**
     * 獲取所有標籤
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags();
        
        return ResponseMaker::success(
            data: $this->transformer->transformCollection($tags),
            message: '所有標籤'
        );
    }

    /**
     * 獲取指定標籤
     * 
     * @param int $id 標籤ID
     * @return JsonResponse
     * @throws ResourceNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->getTagById($id);
        
        return ResponseMaker::success(
            data: $this->transformer->transform($tag),
            message: '標籤詳情'
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
        
        return ResponseMaker::success(
            data: $this->transformer->transform($tag),
            message: '標籤創建成功',
            code: 201
        );
    }

    /**
     * 更新標籤
     * 
     * @param int $id 標籤ID
     * @param UpdateTagRequest $request
     * @return JsonResponse
     * @throws ResourceNotFoundException
     */
    public function update(int $id, UpdateTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tag = $this->tagService->updateTag($id, $data);
        
        return ResponseMaker::success(
            data: $this->transformer->transform($tag),
            message: '標籤更新成功'
        );
    }

    /**
     * 刪除標籤
     * 
     * @param int $id 標籤ID
     * @return JsonResponse
     * @throws ResourceNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->tagService->deleteTag($id);
        
        return ResponseMaker::success(message: '標籤刪除成功');
    }
} 