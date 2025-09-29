import http from './http';
import { API_ROUTES } from './routes';
import type { ApiFunction } from '../types/common';
import type { Tag, CreateTagParams, UpdateTagParams } from '../types/tag';

/**
 * 標籤 API 封裝
 * 提供標籤相關的所有 API 操作
 */
export const tagApi = {
  /**
   * 獲取所有標籤列表（公開API，前後台共用）
   */
  getList: (() => http.get(API_ROUTES.TAG.LIST)
    .then(r => r.data)) as ApiFunction<Tag[]>,

  /**
   * 創建新標籤（管理API，需要認證）
   * @param data 標籤資料
   */
  create: ((data: CreateTagParams) => http.post(API_ROUTES.ADMIN.TAG.CREATE, data)
    .then(r => r.data)) as ApiFunction<Tag, CreateTagParams>,

  /**
   * 更新標籤（管理API，需要認證）
   * @param params 包含 ID 和更新數據的參數
   */
  update: ((params: UpdateTagParams) =>
    http.put(API_ROUTES.ADMIN.TAG.UPDATE(params.id), params.data)
    .then(r => r.data)) as ApiFunction<Tag, UpdateTagParams>,

  /**
   * 刪除標籤（管理API，需要認證）
   * @param id 標籤 ID
   */
  delete: ((id: number) => http.delete(API_ROUTES.ADMIN.TAG.DELETE(id))
    .then(r => r.data)) as ApiFunction<null, number>
};
