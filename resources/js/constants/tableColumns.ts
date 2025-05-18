/**
 * 表格列配置
 */

// 文章表格列
export const ARTICLE_COLUMNS = [
  { title: '標題', key: 'title', sorter: true },
  { title: '分類', key: 'category.name' },
  { title: '狀態', key: 'status' },
  { title: '建立時間', key: 'created_at', sorter: true }
];

// 分類表格列
export const CATEGORY_COLUMNS = [
  { title: '名稱', key: 'name', sorter: true },
  { title: 'Slug', key: 'slug' },
  { title: '描述', key: 'description' },
  { title: '建立時間', key: 'created_at', sorter: true }
];

// 標籤表格列
export const TAG_COLUMNS = [
  { title: '名稱', key: 'name', sorter: true },
  { title: 'Slug', key: 'slug' },
  { title: '建立時間', key: 'created_at', sorter: true }
];

// 狀態選項
export const STATUS_OPTIONS = [
  { label: '全部', value: 'all' },
  { label: '已發布', value: 'published' },
  { label: '草稿', value: 'draft' }
];

// 表格載入文字
export const TABLE_LOADING_TEXT = '載入中...';

// 通用錯誤提示
export const ERROR_MESSAGES = {
  FETCH_FAILED: '獲取數據失敗，請稍後再試',
  CREATE_FAILED: '創建失敗，請稍後再試',
  UPDATE_FAILED: '更新失敗，請稍後再試',
  DELETE_FAILED: '刪除失敗，請稍後再試'
}; 