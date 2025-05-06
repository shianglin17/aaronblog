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
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
  from?: number;
  to?: number;
} 