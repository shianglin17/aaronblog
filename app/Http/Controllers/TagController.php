<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
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

} 