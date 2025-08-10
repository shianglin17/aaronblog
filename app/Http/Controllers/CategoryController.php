<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Transformer\CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    /**
     * @param CategoryTransformer $transformer
     * @param CategoryService $categoryService
     */
    public function __construct(
        protected readonly CategoryTransformer $transformer,
        protected readonly CategoryService $categoryService
    ) {
    }

    /**
     * 獲取所有分類
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        
        return ApiResponse::ok(
            $this->transformer->transformCollection($categories)
        );
    }

    /**
     * 獲取指定分類
     * 
     * @param int $id 分類ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        
        return ApiResponse::ok(
            $this->transformer->transform($category)
        );
    }

    /**
     * 創建分類
     * 
     * @param CreateCategoryRequest $request
     * @return JsonResponse
     */
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data);
        
        return ApiResponse::created(
            $this->transformer->transform($category)
        );
    }

    /**
     * 更新分類
     * 
     * @param int $id 分類ID
     * @param UpdateCategoryRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(int $id, UpdateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->updateCategory($id, $data);
        
        return ApiResponse::ok(
            $this->transformer->transform($category)
        );
    }

    /**
     * 刪除分類
     * 
     * @param int $id 分類ID
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws ResourceInUseException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        
        return ApiResponse::ok();
    }
} 