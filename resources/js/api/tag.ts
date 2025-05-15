import http from './http';
import { API_ROUTES } from './routes';
import { Tag } from '../types/article';
import { ApiResponse } from '../types/common';

/**
 * 獲取所有標籤
 * 
 * @returns Promise 包含標籤列表的 API 響應
 */
export async function getAllTags(): Promise<ApiResponse<Tag[]>> {
  return (await http.get(API_ROUTES.ARTICLE.TAGS)).data;
}

/**
 * 獲取標籤詳情
 * 
 * @param id - 標籤 ID
 * @returns Promise 包含單個標籤詳情的 API 響應
 */
export async function getTagById(id: number): Promise<ApiResponse<Tag>> {
  return (await http.get(API_ROUTES.ARTICLE.TAG_DETAIL(id))).data;
}

/**
 * 創建標籤
 * 
 * @param data - 標籤數據 
 * @param data.name - 標籤名稱
 * @param data.slug - 可選的標籤 slug，如不提供則自動生成
 * @returns Promise 包含創建後標籤數據的 API 響應
 */
export async function createTag(data: { name: string; slug?: string }): Promise<ApiResponse<Tag>> {
  return (await http.post(API_ROUTES.ADMIN.TAG.CREATE, data)).data;
}

/**
 * 更新標籤
 * 
 * @param id - 標籤 ID
 * @param data - 需要更新的標籤數據
 * @param data.name - 可選，更新後的標籤名稱
 * @param data.slug - 可選，更新後的標籤 slug
 * @returns Promise 包含更新後標籤數據的 API 響應
 */
export async function updateTag(id: number, data: { name?: string; slug?: string }): Promise<ApiResponse<Tag>> {
  return (await http.put(API_ROUTES.ADMIN.TAG.UPDATE(id), data)).data;
}

/**
 * 刪除標籤
 * @param id - 標籤 ID
 * @returns Promise 包含刪除操作結果的 API 響應
 */
export async function deleteTag(id: number): Promise<ApiResponse<null>> {
  return (await http.delete(API_ROUTES.ADMIN.TAG.DELETE(id))).data;
} 