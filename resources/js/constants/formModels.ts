/**
 * 表單模型配置
 */
import { 
  TITLE_RULES, 
  SLUG_RULES, 
  DESCRIPTION_RULES,
  CONTENT_RULES,
  NAME_RULES,
  CATEGORY_RULES,
  STATUS_RULES,
  TAGS_RULES
} from './validationRules';

// 文章表單默認值
export const DEFAULT_ARTICLE_FORM = {
  title: '',
  slug: '',
  description: '',
  content: '',
  category_id: null,
  status: 'draft' as 'draft' | 'published',
  tags: []
};

// 文章表單驗證規則
export const ARTICLE_FORM_RULES = {
  title: TITLE_RULES,
  slug: SLUG_RULES,
  description: DESCRIPTION_RULES,
  content: CONTENT_RULES,
  category_id: CATEGORY_RULES,
  status: STATUS_RULES,
  tags: TAGS_RULES
};

// 分類表單默認值
export const DEFAULT_CATEGORY_FORM = {
  name: '',
  slug: '',
  description: ''
};

// 分類表單驗證規則
export const CATEGORY_FORM_RULES = {
  name: NAME_RULES,
  slug: SLUG_RULES,
  description: DESCRIPTION_RULES
};

// 標籤表單默認值
export const DEFAULT_TAG_FORM = {
  name: '',
  slug: ''
};

// 標籤表單驗證規則
export const TAG_FORM_RULES = {
  name: NAME_RULES,
  slug: SLUG_RULES
}; 