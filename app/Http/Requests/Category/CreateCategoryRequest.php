<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    /**
     * 判斷使用者是否有權限執行此請求
     */
    public function authorize(): bool
    {
        return true; // 在此階段允許任何已登入用戶創建分類，後續可以加入權限控制
    }

    /**
     * 獲取應用於此請求的驗證規則
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug', 'alpha_dash'],
            'description' => ['required', 'string', 'max:500']
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
            'name.required' => '分類名稱不能為空',
            'name.max' => '分類名稱不能超過50個字符',
            'name.unique' => '分類名稱已存在',
            'slug.required' => '分類別名不能為空',
            'slug.max' => '分類別名不能超過255個字符',
            'slug.unique' => '分類別名已存在',
            'slug.alpha_dash' => '分類別名只能包含字母、數字、連字符和底線',
            'description.required' => '分類描述不能為空',
            'description.max' => '分類描述不能超過500個字符'
        ];
    }
} 