import http from './http';
import { API_ROUTES } from './routes';
import { Article, ArticleListParams, Category, Tag } from '../types/article';
import { ApiResponse } from '../types/common';

/**
 * 獲取文章列表
 * @param params - 文章篩選與分頁參數
 * @returns Promise 包含文章列表的 API 響應
 */
export async function getArticleList(params: ArticleListParams = {}): Promise<ApiResponse<Article[]>> {
  return (await http.get(API_ROUTES.ARTICLE.LIST, { params })).data;
}

/**
 * 獲取單篇文章
 * @param id - 文章 ID
 * @returns Promise 包含單篇文章詳情的 API 響應
 */
export async function getArticleById(id: number): Promise<ApiResponse<Article>> {
  return (await http.get(API_ROUTES.ARTICLE.DETAIL(id))).data;
}

/**
 * 獲取所有分類
 * @returns Promise 包含分類列表的 API 響應
 */
export async function getAllCategories(): Promise<ApiResponse<Category[]>> {
  return (await http.get(API_ROUTES.ARTICLE.CATEGORIES)).data;
}

/**
 * 獲取所有標籤
 * @returns Promise 包含標籤列表的 API 響應
 */
export async function getAllTags(): Promise<ApiResponse<Tag[]>> {
  return (await http.get(API_ROUTES.ARTICLE.TAGS)).data;
}