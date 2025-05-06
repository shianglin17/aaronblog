import http from './http';
import { Article, ArticleListParams } from '../types/article';
import { ApiResponse } from '../types/common';

// 獲取文章列表
export async function getArticleList(params: ArticleListParams = {}): Promise<ApiResponse<Article[]>> {
  try {
    const response = await http.get('/article/list', { params });
    return response.data;
  } catch (error) {
    console.error('獲取文章列表失敗:', error);
    throw error;
  }
}