<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Response\ResponseMaker;
use App\Transformer\CategoryTransformer;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected readonly CategoryTransformer $transformer;

    public function __construct(CategoryTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * 獲取所有分類
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        
        return ResponseMaker::success(
            data: $this->transformer->transformCollection($categories),
            message: '所有分類'
        );
    }
} 