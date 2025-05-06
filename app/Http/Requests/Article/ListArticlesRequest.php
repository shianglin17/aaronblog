<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ListArticlesRequest extends FormRequest
{
    /**
     * 允許的排序欄位
     *
     * @var array<string>
     */
    protected array $allowedSortFields = [
        'created_at',
        'updated_at',
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
     * 預設值設定
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'page' => 1,
        'per_page' => 15,
        'sort_by' => 'created_at',
        'sort_direction' => 'desc',
        'search' => ''
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
            'page' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100'
            ],
            'sort_by' => [
                'nullable',
                'string',
                'in:' . implode(',', $this->allowedSortFields)
            ],
            'sort_direction' => [
                'nullable',
                'string',
                'in:' . implode(',', $this->allowedSortDirections)
            ],
            'search' => [
                'nullable',
                'string',
                'max:255'
            ]
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
            'per_page.max' => '每頁筆數不能超過 100',
            'sort_by.in' => '排序欄位必須是 ' . implode(', ', $this->allowedSortFields) . ' 其中之一',
            'sort_direction.in' => '排序方向必須是 ' . implode(', ', $this->allowedSortDirections) . ' 其中之一',
            'search.max' => '搜尋關鍵字不能超過 255 個字元'
        ];
    }

    /**
     * 前置處理請求資料
     */
    protected function prepareForValidation(): void
    {
        // 我這邊要設計一個機制，說如果前端沒有送參數的 key 我會幫她補上預設值，要怎麼做？
        $this->merge([
            'page' => $this->input('page') ? $this->input('page') : $this->defaults['page'],
            'per_page' => $this->input('per_page') ? $this->input('per_page') : $this->defaults['per_page'],
            'sort_by' => $this->input('sort_by') ? $this->input('sort_by') : $this->defaults['sort_by'],
            'sort_direction' => $this->input('sort_direction') ? $this->input('sort_direction') : $this->defaults['sort_direction'],
            'search' => $this->input('search') ? trim($this->input('search')) : null,
        ]);
    }
}
