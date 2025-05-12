<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Response\ResponseMaker;
use App\Transformer\TagTransformer;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    protected readonly TagTransformer $transformer;

    public function __construct(TagTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * 獲取所有標籤
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = Tag::all();
        
        return ResponseMaker::success(
            data: $this->transformer->transformCollection($tags),
            message: '所有標籤'
        );
    }
} 