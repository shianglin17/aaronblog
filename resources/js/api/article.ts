import http from './http';
import { API_ROUTES } from './routes';
import { Article, ArticleListParams, Category, Tag } from '../types/article';
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

// 獲取所有分類
export async function getAllCategories(): Promise<ApiResponse<Category[]>> {
  try {
    const response = await http.get(API_ROUTES.ARTICLE.CATEGORIES);
    return response.data;
  } catch (error) {
    console.error('獲取分類列表失敗:', error);
    throw error;
  }
}

// 獲取所有標籤
export async function getAllTags(): Promise<ApiResponse<Tag[]>> {
  try {
    const response = await http.get(API_ROUTES.ARTICLE.TAGS);
    return response.data;
  } catch (error) {
    console.error('獲取標籤列表失敗:', error);
    throw error;
  }
}

// 新增文章
export async function createArticle(data: any): Promise<ApiResponse<any>> {
  try {
    const response = await http.post('/admin/article', data);
    return response.data;
  } catch (error) {
    console.error('新增文章失敗:', error);
    throw error;
  }
}

// 更新文章
export async function updateArticle(id: number, data: any): Promise<ApiResponse<any>> {
  try {
    const response = await http.put(`/admin/article/${id}`, data);
    return response.data;
  } catch (error) {
    console.error('更新文章失敗:', error);
    throw error;
  }
}

// 刪除文章
export async function deleteArticle(id: number): Promise<ApiResponse<any>> {
  try {
    const response = await http.delete(`/admin/article/${id}`);
    return response.data;
  } catch (error) {
    console.error('刪除文章失敗:', error);
    throw error;
  }
}

// 設為草稿
export async function setArticleDraft(id: number): Promise<ApiResponse<any>> {
  try {
    const response = await http.patch(`/admin/article/${id}/draft`);
    return response.data;
  } catch (error) {
    console.error('設為草稿失敗:', error);
    throw error;
  }
}

// 發布文章
export async function setArticlePublish(id: number): Promise<ApiResponse<any>> {
  try {
    const response = await http.patch(`/admin/article/${id}/publish`);
    return response.data;
  } catch (error) {
    console.error('發布文章失敗:', error);
    throw error;
  }
}