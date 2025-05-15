<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class CreateTagRequest extends FormRequest
{
    /**
     * 判斷使用者是否有權限執行此請求
     */
    public function authorize(): bool
    {
        return true; // 在此階段允許任何已登入用戶創建標籤，後續可以加入權限控制
    }

    /**
     * 獲取應用於此請求的驗證規則
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tags,slug', 'alpha_dash']
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
            'name.required' => '標籤名稱不能為空',
            'name.max' => '標籤名稱不能超過50個字符',
            'name.unique' => '標籤名稱已存在',
            'slug.max' => 'Slug不能超過255個字符',
            'slug.unique' => 'Slug已存在',
            'slug.alpha_dash' => 'Slug只能包含字母、數字、連字符和底線'
        ];
    }
} 