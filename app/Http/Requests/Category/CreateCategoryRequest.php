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

} 