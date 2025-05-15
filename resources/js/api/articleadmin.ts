import http from './http';
import { ApiResponse } from '../types/common';
import { API_ROUTES } from './routes';
import { Article } from '../types/article';

/**
 * 新增文章
 * @param data - 文章數據
 * @returns Promise 包含創建後文章數據的 API 響應
 */
export async function createArticle(data: any): Promise<ApiResponse<Article>> {
  return (await http.post(API_ROUTES.ADMIN.ARTICLE.CREATE, data)).data;
}

/**
 * 更新文章
 * @param id - 文章 ID
 * @param data - 需要更新的文章數據
 * @returns Promise 包含更新後文章數據的 API 響應
 */
export async function updateArticle(id: number, data: any): Promise<ApiResponse<Article>> {
  return (await http.put(API_ROUTES.ADMIN.ARTICLE.UPDATE(id), data)).data;
}

/**
 * 刪除文章
 * @param id - 文章 ID
 * @returns Promise 包含刪除操作結果的 API 響應
 */
export async function deleteArticle(id: number): Promise<ApiResponse<null>> {
  return (await http.delete(API_ROUTES.ADMIN.ARTICLE.DELETE(id))).data;
}

/**
 * 設為草稿
 * @param id - 文章 ID
 * @returns Promise 包含更新後的文章數據的 API 響應
 */
export async function setArticleDraft(id: number): Promise<ApiResponse<Article>> {
  return (await http.patch(API_ROUTES.ADMIN.ARTICLE.DRAFT(id))).data;
}

/**
 * 發布文章
 * @param id - 文章 ID
 * @returns Promise 包含更新後的文章數據的 API 響應
 */
export async function setArticlePublish(id: number): Promise<ApiResponse<Article>> {
  return (await http.patch(API_ROUTES.ADMIN.ARTICLE.PUBLISH(id))).data;
} 