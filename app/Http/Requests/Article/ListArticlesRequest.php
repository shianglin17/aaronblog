<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
     * 允許的文章狀態
     * 
     * @var array<string>
     */
    protected array $allowedStatus = [
        Article::STATUS_DRAFT,
        Article::STATUS_PUBLISHED,
        'all',
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
        'search' => '',
        'status' => null,
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
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable','string','in:' . implode(',', $this->allowedSortFields)],
            'sort_direction' => ['nullable','string','in:' . implode(',', $this->allowedSortDirections)],
            'search' => ['nullable','string','max:255'],
            'status' => ['nullable','string','in:' . implode(',', $this->allowedStatus)],
            'category' => ['nullable','string','max:255'],
            'tags' => ['nullable', 'array'],  // 明確要求陣列格式
            'tags.*' => ['string']  // 標籤名稱或ID
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
            'search.max' => '搜尋關鍵字不能超過 255 個字元',
            'status.in' => '文章狀態必須是 ' . implode(', ', $this->allowedStatus) . ' 其中之一',
            'category.string' => '分類必須是字串',
            'category.max' => '分類不能超過 255 個字元'
        ];
    }

    /**
     * 前置處理請求資料
     * 
     * 基於路由的認證感知參數處理：
     * 1. 公開路由 (/api/articles)：強制只顯示已發布文章，確保公開內容的安全性
     * 2. 管理路由 (/api/admin/articles)：需要通過認證，認證後可彈性控制 status 參數
     * 3. 認證用戶若未指定狀態，預設顯示所有狀態文章以便管理
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
        ];

        // 處理 status 參數的路由感知邏輯
        if (request()->is('api/articles')) {
            // 公開路由：強制只顯示已發布文章，確保未認證用戶只能看到公開內容
            $mergeData['status'] = Article::STATUS_PUBLISHED;
        } else if (request()->is('api/admin/articles') && Auth::check()) {
            // 管理路由且已指定狀態：允許認證用戶透過參數控制查詢範圍
            $mergeData['status'] = $this->input('status');
        }
        
        $this->merge($mergeData);
    }
}
