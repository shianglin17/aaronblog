/**
 * 通用類型定義
 */

// 定義通用API響應介面
export interface ApiResponse<T> {
  status: string;
  code: number;
  message: string;
  data: T;
  meta?: {
    pagination?: PaginationMeta;
    [key: string]: any;
  };
}

// 分頁元數據
export interface PaginationMeta {
  current_page: number;
  total_pages: number;
  total_items: number;
  per_page: number;
  from?: number;
  to?: number;
} 