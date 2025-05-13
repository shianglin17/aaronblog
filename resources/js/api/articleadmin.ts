import http from './http';
import { ApiResponse } from '../types/common';
import { API_ROUTES } from './routes';

// 新增文章
export async function createArticle(data: any): Promise<ApiResponse<any>> {
  return (await http.post(API_ROUTES.ADMIN.ARTICLE.CREATE, data)).data;
}

// 更新文章
export async function updateArticle(id: number, data: any): Promise<ApiResponse<any>> {
  return (await http.put(API_ROUTES.ADMIN.ARTICLE.UPDATE(id), data)).data;
}

// 刪除文章
export async function deleteArticle(id: number): Promise<ApiResponse<any>> {
  return (await http.delete(API_ROUTES.ADMIN.ARTICLE.DELETE(id))).data;
}

// 設為草稿
export async function setArticleDraft(id: number): Promise<ApiResponse<any>> {
  return (await http.patch(API_ROUTES.ADMIN.ARTICLE.DRAFT(id))).data;
}

// 發布文章
export async function setArticlePublish(id: number): Promise<ApiResponse<any>> {
  return (await http.patch(API_ROUTES.ADMIN.ARTICLE.PUBLISH(id))).data;
} 