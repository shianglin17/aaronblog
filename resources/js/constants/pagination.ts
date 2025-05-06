/**
 * 分頁相關常量
 */

export const DEFAULT_PAGE = 1;
export const DEFAULT_PAGE_SIZE = 10;
export const DEFAULT_SORT_BY = 'created_at' as const;
export const DEFAULT_SORT_DIRECTION = 'desc' as const;

export const PAGE_SIZE_OPTIONS = [10, 20, 50];

// 預設的分頁參數
export const DEFAULT_PAGINATION_PARAMS = {
  page: DEFAULT_PAGE,
  per_page: DEFAULT_PAGE_SIZE,
  sort_by: DEFAULT_SORT_BY,
  sort_direction: DEFAULT_SORT_DIRECTION
}; 