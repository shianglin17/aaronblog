<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class GetArticleRequest extends FormRequest
{
    /**
     * 確定用戶是否有權發出此請求
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 獲取適用於請求的驗證規則
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:articles,id',
        ];
    }

    /**
     * 獲取已定義驗證規則的錯誤消息
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => '文章ID不能為空',
            'id.integer' => '文章ID必須為整數',
            'id.exists' => '文章不存在',
        ];
    }
} 