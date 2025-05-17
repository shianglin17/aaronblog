/**
 * 分類相關類型定義
 */

// 分類實體介面
export interface Category {
  id: number;
  name: string;
  slug: string;
}

// 創建分類請求參數介面
export interface CreateCategoryParams {
  name: string;
  slug: string;
}

// 更新分類請求參數介面
export interface UpdateCategoryParams {
  id: number;
  data: {
    name?: string;
    slug: string;
  }
} 