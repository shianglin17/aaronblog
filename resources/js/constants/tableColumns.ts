/**
 * 表格列配置
 */

import { h } from 'vue';
import { NTag } from 'naive-ui';
import { formatDateTime } from '../utils/date';

// 狀態選項（統一定義，包含顯示和篩選所需資訊）
export const STATUS_OPTIONS = [
  { label: '全部', value: 'all' },
  { label: '已發布', value: 'published', tagType: 'success' as const },
  { label: '草稿', value: 'draft', tagType: 'warning' as const }
];

// 根據狀態值獲取狀態資訊的工具函數
function getStatusInfo(status: string) {
  return STATUS_OPTIONS.find(option => option.value === status);
}

// 文章表格列
export const ARTICLE_COLUMNS = [
  { title: '標題', key: 'title', sorter: true },
  { title: '分類', key: 'category.name' },
  { 
    title: '狀態', 
    key: 'status',
    render(row: any) {
      const statusInfo = getStatusInfo(row.status);
      if (!statusInfo || !statusInfo.tagType) return row.status;
      
      return h(
        NTag,
        { type: statusInfo.tagType },
        { default: () => statusInfo.label }
      );
    }
  },
  { 
    title: '建立時間', 
    key: 'created_at', 
    sorter: true,
    render(row: any) {
      return formatDateTime(row.created_at);
    }
  }
];

// 分類表格列
export const CATEGORY_COLUMNS = [
  { title: '名稱', key: 'name', sorter: true },
  { title: 'Slug', key: 'slug' },
  { title: '描述', key: 'description' },
  { title: '文章數量', key: 'articles_count' },
  { 
    title: '建立時間', 
    key: 'created_at', 
    sorter: true,
    render(row: any) {
      return formatDateTime(row.created_at);
    }
  }
];

// 標籤表格列
export const TAG_COLUMNS = [
  { title: '名稱', key: 'name', sorter: true },
  { title: 'Slug', key: 'slug' },
  { title: '文章數量', key: 'articles_count' },
  { 
    title: '建立時間', 
    key: 'created_at', 
    sorter: true,
    render(row: any) {
      return formatDateTime(row.created_at);
    }
  }
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