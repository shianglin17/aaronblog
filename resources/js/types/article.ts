// 定義文章介面
export interface Article {
  id?: number;
  user_id: number;
  category_id: number;
  title: string;
  content: string;
  created_at: string;
  updated_at?: string;
}

// 定義請求參數介面
export interface ArticleListParams {
  page?: number;
  per_page?: number;
  sort_by?: 'created_at' | 'updated_at' | 'title';
  sort_direction?: 'asc' | 'desc';
  search?: string;
}

// 定義分頁資訊介面
export interface Pagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
  from?: number;
  to?: number;
} 