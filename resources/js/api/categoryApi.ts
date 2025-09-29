import http from './http';
import { API_ROUTES } from './routes';
import type { ApiFunction } from '../types/common';
import type { Category, CreateCategoryParams, UpdateCategoryParams } from '../types/category';

/**
 * 分類 API 封裝
 * 提供分類相關的所有 API 操作
 */
export const categoryApi = {
  /**
   * 獲取所有分類列表
   */
  getList: (() => http.get(API_ROUTES.CATEGORY.LIST)
    .then(r => r.data)) as ApiFunction<Category[]>,

  /**
   * 創建新分類
   * @param data 分類資料 (slug 為必填欄位)
   */
  create: ((data: CreateCategoryParams) => http.post(API_ROUTES.ADMIN.CATEGORY.CREATE, data)
    .then(r => r.data)) as ApiFunction<Category, CreateCategoryParams>,

  /**
   * 更新分類
   * @param params 包含 ID 和更新數據的參數 (slug 為必填欄位)
   */
  update: ((params: UpdateCategoryParams) =>
    http.put(API_ROUTES.ADMIN.CATEGORY.UPDATE(params.id), params.data)
    .then(r => r.data)) as ApiFunction<Category, UpdateCategoryParams>,

  /**
   * 刪除分類
   * @param id 分類 ID
   */
  delete: ((id: number) => http.delete(API_ROUTES.ADMIN.CATEGORY.DELETE(id))
    .then(r => r.data)) as ApiFunction<null, number>
};
