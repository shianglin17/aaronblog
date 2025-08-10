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
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('articles')->ignore($articleId), 'alpha_dash'],
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'status' => ['sometimes', 'string', 'in:' . Article::STATUS_DRAFT . ',' . Article::STATUS_PUBLISHED],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id']
        ];
    }

}
