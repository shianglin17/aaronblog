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

// 分頁 meta
export interface PaginationMeta {
  current_page: number;
  total_pages: number;
  total_items: number;
  per_page: number;
} 

// API函數類型定義
export type ApiFunction<T, P = void> = P extends void 
  ? () => Promise<ApiResponse<T>> 
  : (params: P) => Promise<ApiResponse<T>>; 