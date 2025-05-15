<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Response\ResponseMaker;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Transformer\CategoryTransformer;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected readonly CategoryTransformer $transformer;
    protected readonly CategoryService $categoryService;

    public function __construct(CategoryTransformer $transformer, CategoryService $categoryService)
    {
        $this->transformer = $transformer;
        $this->categoryService = $categoryService;
    }

    /**
     * 獲取所有分類
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        
        return ResponseMaker::success(
            data: $this->transformer->transformCollection($categories),
            message: '所有分類'
        );
    }

    /**
     * 獲取指定分類
     * 
     * @param int $id 分類ID
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        
        if (!$category) {
            return ResponseMaker::error('分類不存在', 404);
        }
        
        return ResponseMaker::success(
            data: $this->transformer->transform($category),
            message: '分類詳情'
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
        
        return ResponseMaker::success(
            data: $this->transformer->transform($category),
            message: '分類創建成功',
            code: 201
        );
    }

    /**
     * 更新分類
     * 
     * @param int $id 分類ID
     * @param UpdateCategoryRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->updateCategory($id, $data);
        
        if (!$category) {
            return ResponseMaker::error('分類不存在', 404);
        }
        
        return ResponseMaker::success(
            data: $this->transformer->transform($category),
            message: '分類更新成功'
        );
    }

    /**
     * 刪除分類
     * 
     * @param int $id 分類ID
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->categoryService->deleteCategory($id);
        
        if (!$result) {
            return ResponseMaker::error('分類不存在', 404);
        }
        
        return ResponseMaker::success(message: '分類刪除成功');
    }
} 