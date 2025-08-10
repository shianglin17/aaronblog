<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 驗證語言檔
    |--------------------------------------------------------------------------
    |
    | 以下語言檔包含驗證類使用的預設錯誤訊息。
    | 你可以根據需要自由調整這些訊息。
    |
    */

    'required' => ':attribute 不能為空',
    'unique' => ':attribute 已存在',
    'alpha_dash' => ':attribute 只能包含字母、數字、連字符和底線',
    'string' => ':attribute 必須是字串',
    'integer' => ':attribute 必須是整數',
    'nullable' => ':attribute 可以為空',
    'array' => ':attribute 必須是陣列',
    'exists' => '所選的 :attribute 不存在',
    'in' => ':attribute 的值無效',
    'max' => [
        'array' => ':attribute 不能超過 :max 個項目',
        'file' => ':attribute 不能超過 :max KB',
        'numeric' => ':attribute 不能大於 :max',
        'string' => ':attribute 不能超過 :max 個字符',
    ],
    'min' => [
        'array' => ':attribute 至少要有 :min 個項目',
        'file' => ':attribute 至少要有 :min KB',
        'numeric' => ':attribute 不能小於 :min',
        'string' => ':attribute 至少要有 :min 個字符',
    ],
    'sometimes' => ':attribute 是可選的',
    'nullable' => ':attribute 可以為空',

    /*
    |--------------------------------------------------------------------------
    | 自訂驗證屬性名稱
    |--------------------------------------------------------------------------
    |
    | 以下語言檔用來將屬性佔位符替換為更易讀的內容，
    | 例如用「電子郵件地址」代替「email」，這讓訊息更易理解。
    |
    */

    'attributes' => [
        'name' => '名稱',
        'slug' => '別名',
        'description' => '描述',
        'title' => '標題',
        'content' => '內容',
        'status' => '狀態',
        'category_id' => '分類',
        'tags' => '標籤',
        'tags.*' => '標籤項目',
    ],

];