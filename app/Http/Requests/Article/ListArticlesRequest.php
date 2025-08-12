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
            'search' => ['sometimes', 'string', 'max:100'],  // 前台搜尋限制更短
            'category' => ['sometimes', 'string', 'max:255'],
            'tags' => ['sometimes', 'array', 'max:5'],  // 前台標籤限制數量
            'tags.*' => ['string', 'max:255']
        ];
    }

    /**
     * 取得驗證訊息
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => '頁碼必須是整數',
            'page.min' => '頁碼必須大於等於 1',
            'per_page.integer' => '每頁筆數必須是整數',
            'per_page.min' => '每頁筆數必須大於等於 1',
            'per_page.max' => '每頁筆數不能超過 50',
            'sort_by.in' => '排序欄位必須是 ' . implode(', ', $this->allowedSortFields) . ' 其中之一',
            'sort_direction.in' => '排序方向必須是 ' . implode(', ', $this->allowedSortDirections) . ' 其中之一',
            'search.max' => '搜尋關鍵字不能超過 100 個字元',
            'category.max' => '分類不能超過 255 個字元',
            'tags.max' => '最多只能選擇 5 個標籤',
            'tags.*.max' => '標籤長度不能超過 255 個字元'
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
            'search' => $this->has('search') ? trim($this->input('search')) : null,
            'category' => $this->has('category') ? trim($this->input('category')) : null,
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
