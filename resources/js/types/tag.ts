/**
 * 標籤相關類型定義
 */

// 標籤實體介面
export interface Tag {
  id: number;
  name: string;
  slug: string;
  articles_count: number;
  created_at: string;
}

// 創建標籤請求參數介面
export interface CreateTagParams {
  name: string;
  slug: string;
}

// 更新標籤請求參數介面
export interface UpdateTagParams {
  id: number;
  data: {
    name?: string;
    slug?: string;
  }
} 