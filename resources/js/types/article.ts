import { Tag } from './tag';
import { Category } from './category';

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

// 定義創建文章所需的參數
export interface CreateArticleParams {
  title: string;
  slug?: string;
  description?: string;
  content: string;
  category_id?: number | null;
  status: 'draft' | 'published';
  tags?: number[]; // 標籤 ID 陣列，後端會處理成 Tag 物件
}

// 定義請求參數介面
export interface ArticleListParams {
  page?: number;
  per_page?: number;
  sort_by?: 'created_at' | 'updated_at' | 'title';
  sort_direction?: 'asc' | 'desc';
  search?: string;
  status?: 'draft' | 'published' | 'all';
  category?: string;
  tags?: string[];
}
