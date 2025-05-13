import http from './http';
import { ApiResponse } from '../types/common';
import { Article } from '../types/article';

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