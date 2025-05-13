// 定義文章介面
export interface Article {
  id?: number;
  title: string;
  slug?: string;
  description?: string;
  content: string;
  status: 'draft' | 'published';
  author: {
    id: number;
    name: string;
  };
  category: Category;
  created_at: string;
  updated_at?: string;
  tags?: Tag[];
}

// 定義分類介面
export interface Category {
  id: number;
  name: string;
  slug: string;
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
  status?: 'draft' | 'published';
  category?: string;
  tags?: string[];
}
