// 定義文章介面
export interface Article {
  id?: number;
  user_id: number;
  user_name: string;
  category_id: number;
  category_name: string;
  title: string;
  content: string;
  created_at: string;
  updated_at?: string;
  tags?: Tag[];
}

// 定義標籤介面
export interface Tag {
  id: number;
  name: string;
  slug?: string;
}

// 定義請求參數介面
export interface ArticleListParams {
  page?: number;
  per_page?: number;
  sort_by?: 'created_at' | 'updated_at' | 'title';
  sort_direction?: 'asc' | 'desc';
  search?: string;
}
