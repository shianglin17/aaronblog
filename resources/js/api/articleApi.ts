import http from './http';
import { API_ROUTES } from './routes';
import type { Article, CreateArticleParams, ArticleListParams } from '../types/article';
import type { ApiFunction } from '../types/common';

export const articleApi = {
  admin: {
    getList: ((params: ArticleListParams = {}) =>
      http.get(API_ROUTES.ADMIN.ARTICLE.LIST, { params }).then(r => r.data)) as ApiFunction<Article[], ArticleListParams>,

    create: ((data: CreateArticleParams) =>
      http.post(API_ROUTES.ADMIN.ARTICLE.CREATE, data).then(r => r.data)) as ApiFunction<Article, CreateArticleParams>,
    
    update: ((params: { id: number; data: Partial<CreateArticleParams> }) =>
      http.put(API_ROUTES.ADMIN.ARTICLE.UPDATE(params.id), params.data).then(r => r.data)) as ApiFunction<Article, { id: number; data: Partial<CreateArticleParams> }>,
    
    delete: ((id: number) =>
      http.delete(API_ROUTES.ADMIN.ARTICLE.DELETE(id)).then(r => r.data)) as ApiFunction<null, number>
  }
};
