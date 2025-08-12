<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class AdminListArticlesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:all,published,draft'],
            'category' => ['sometimes', 'string', 'max:255'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:255'],
            'sort_by' => ['sometimes', 'string', 'in:created_at,updated_at,title'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get the validated data with defaults for admin articles
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        return array_merge([
            'page' => 1,
            'per_page' => 15,
            'search' => '',
            'status' => 'all',  // 後台預設顯示所有狀態
            'sort_by' => 'created_at',
            'sort_direction' => 'desc',
        ], $validated);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // 確保 tags 陣列中沒有重複值
            if ($this->has('tags') && is_array($this->input('tags'))) {
                $tags = $this->input('tags');
                if (count($tags) !== count(array_unique($tags))) {
                    $validator->errors()->add('tags', '標籤不能重複');
                }
            }
        });
    }
}