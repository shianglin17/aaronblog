import http from './http';
import { API_ROUTES } from './routes';
import { Article, ArticleListParams, Category, Tag } from '../types/article';
import { ApiResponse } from '../types/common';

// 獲取文章列表
export async function getArticleList(params: ArticleListParams = {}): Promise<ApiResponse<Article[]>> {
  return (await http.get(API_ROUTES.ARTICLE.LIST, { params })).data;
}

// 獲取單篇文章
export async function getArticleById(id: number): Promise<ApiResponse<Article>> {
  return (await http.get(API_ROUTES.ARTICLE.DETAIL(id))).data;
}

// 獲取所有分類
export async function getAllCategories(): Promise<ApiResponse<Category[]>> {
  return (await http.get(API_ROUTES.ARTICLE.CATEGORIES)).data;
}

// 獲取所有標籤
export async function getAllTags(): Promise<ApiResponse<Tag[]>> {
  return (await http.get(API_ROUTES.ARTICLE.TAGS)).data;
}