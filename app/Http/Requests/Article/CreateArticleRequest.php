<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // 在此階段允許任何已登入用戶建立文章，後續可以加入權限控制
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:articles,title'],
            'slug' => ['required', 'string', 'max:255', 'unique:articles,slug', 'alpha_dash'],
            'description' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', 'string', 'in:' . Article::STATUS_DRAFT . ',' . Article::STATUS_PUBLISHED],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id']
        ];
    }

}
