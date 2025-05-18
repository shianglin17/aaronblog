/**
 * 通用表單驗證規則
 */

// 通用必填規則
export const REQUIRED_RULE = {
  required: true,
  message: '此欄位為必填',
  trigger: ['blur', 'change']
};

// 標題驗證規則
export const TITLE_RULES = [
  REQUIRED_RULE,
  {
    min: 2,
    max: 100,
    message: '標題長度應為 2-100 個字元',
    trigger: ['blur', 'change']
  }
];

// Slug 驗證規則
export const SLUG_RULES = [
  REQUIRED_RULE,
  {
    pattern: /^[a-z0-9-]+$/,
    message: 'Slug 只能包含小寫字母、數字和連字符',
    trigger: ['blur', 'change']
  },
  {
    min: 3,
    max: 100,
    message: 'Slug 長度應為 3-100 個字元',
    trigger: ['blur', 'change']
  }
];

// 描述驗證規則
export const DESCRIPTION_RULES = [
  {
    max: 255,
    message: '描述最多 255 個字元',
    trigger: ['blur', 'change']
  }
];

// 名稱驗證規則
export const NAME_RULES = [
  REQUIRED_RULE,
  {
    min: 2,
    max: 50,
    message: '名稱長度應為 2-50 個字元',
    trigger: ['blur', 'change']
  }
];

// 文章內容驗證規則
export const CONTENT_RULES = [
  REQUIRED_RULE,
  {
    min: 10,
    message: '內容至少需要 10 個字元',
    trigger: ['blur', 'change']
  }
];

// 分類選擇驗證規則
export const CATEGORY_RULES = [
  {
    type: 'number',
    required: true,
    message: '請選擇分類',
    trigger: ['blur', 'change']
  }
];

// 標籤選擇驗證規則
export const TAGS_RULES = [
  {
    type: 'array',
    message: '請選擇至少一個標籤',
    trigger: ['blur', 'change']
  }
];

// 狀態選擇驗證規則
export const STATUS_RULES = [
  {
    type: 'string',
    required: true,
    message: '請選擇狀態',
    trigger: ['blur', 'change']
  }
]; 