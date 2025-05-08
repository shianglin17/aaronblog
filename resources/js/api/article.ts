import http from './http';
import { API_ROUTES } from './routes';
import { Article, ArticleListParams } from '../types/article';
import { ApiResponse } from '../types/common';

// 獲取文章列表
export async function getArticleList(params: ArticleListParams = {}): Promise<ApiResponse<Article[]>> {
  try {
    const response = await http.get(API_ROUTES.ARTICLE.LIST, { params });
    return response.data;
  } catch (error) {
    console.error('獲取文章列表失敗:', error);
    throw error;
  }
}

// 獲取單篇文章
export async function getArticleById(id: number): Promise<ApiResponse<Article>> {
  try {
    const response = await http.get(API_ROUTES.ARTICLE.DETAIL(id));
    return response.data;
  } catch (error) {
    console.error(`獲取文章 ID:${id} 失敗:`, error);
    throw error;
  }
}