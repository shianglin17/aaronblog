<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // 在此階段允許任何已登入用戶更新文章，後續可以加入權限控制
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('id');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('articles')->ignore($articleId)],
            'content' => ['sometimes', 'required', 'string'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'status' => ['sometimes', 'string', 'in:' . Article::STATUS_DRAFT . ',' . Article::STATUS_PUBLISHED],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id']
        ];
    }

    /**
     * 獲取屬性的自定義錯誤訊息
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => '標題不能為空',
            'title.max' => '標題不能超過255個字符',
            'title.unique' => '標題已存在',
            'content.required' => '內容不能為空',
            'category_id.exists' => '所選分類不存在',
            'status.in' => '狀態值無效',
            'tags.array' => '標籤必須是陣列',
            'tags.*.exists' => '標籤不存在'
        ];
    }
}
