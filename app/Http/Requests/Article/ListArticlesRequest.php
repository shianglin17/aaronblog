<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ListArticlesRequest extends FormRequest
{
    /**
     * 允許的排序欄位（前台簡化版）
     *
     * @var array<string>
     */
    protected array $allowedSortFields = [
        'created_at',
        'title'
    ];

    /**
     * 允許的排序方向
     *
     * @var array<string>
     */
    protected array $allowedSortDirections = [
        'asc',
        'desc'
    ];

    /**
     * 預設值設定（前台專用）
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'page' => 1,
        'per_page' => 10,  // 前台預設較少
        'sort_by' => 'created_at',
        'sort_direction' => 'desc',
        'search' => '',
        'category' => null,
        'tags' => []
    ];

    /**
     * 判斷使用者是否有權限執行此請求
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],  // 前台限制更低
            'sort_by' => ['sometimes', 'string', 'in:' . implode(',', $this->allowedSortFields)],
            'sort_direction' => ['sometimes', 'string', 'in:' . implode(',', $this->allowedSortDirections)],
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],  // 前台搜尋限制更短
            'category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tags' => ['sometimes', 'array', 'max:5'],  // 前台標籤限制數量
            'tags.*' => ['string', 'max:255']
        ];
    }

    /**
     * 前置處理請求資料（前台專用）
     * 
     * 前台只顯示已發布文章，確保公開內容的安全性
     */
    protected function prepareForValidation(): void
    {
        $mergeData = [
            'page' => $this->input('page') ?: $this->defaults['page'],
            'per_page' => $this->input('per_page') ?: $this->defaults['per_page'],
            'sort_by' => $this->input('sort_by') ?: $this->defaults['sort_by'],
            'sort_direction' => $this->input('sort_direction') ?: $this->defaults['sort_direction'],
            'search' => $this->input('search') ? trim($this->input('search')) : $this->defaults['search'],
            'category' => $this->input('category') ? trim($this->input('category')) : $this->defaults['category'],
            'tags' => $this->input('tags', $this->defaults['tags']),
            'status' => 'published',  // 前台強制只顯示已發布文章
        ];
        
        $this->merge($mergeData);
    }

    /**
     * Get validated data with defaults applied
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        return array_merge($this->defaults, $validated);
    }
}
