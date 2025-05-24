import http from './http';
import { API_ROUTES } from './routes';
import { Article, CreateArticleParams, ArticleListParams } from '../types/article';
import { Category  } from '../types/category';
import { ApiFunction } from '../types/common';

export const articleApi = {
  // 公共 API
  getList: ((params: ArticleListParams = {}) => 
    http.get(API_ROUTES.ARTICLE.LIST, { params }).then(r => r.data)) as ApiFunction<Article[], ArticleListParams>,
  
  getById: ((id: number) => 
    http.get(API_ROUTES.ARTICLE.DETAIL(id)).then(r => r.data)) as ApiFunction<Article, number>,
  
  getAllCategories: (() => 
    http.get(API_ROUTES.CATEGORY.LIST).then(r => r.data)) as ApiFunction<Category[]>,
  
  // 管理 API
  admin: {
    getList: ((params: ArticleListParams = {}) => 
      http.get(API_ROUTES.ADMIN.ARTICLE.LIST, { params }).then(r => r.data)) as ApiFunction<Article[], ArticleListParams>,

    create: ((data: CreateArticleParams) => 
      http.post(API_ROUTES.ADMIN.ARTICLE.CREATE, data).then(r => r.data)) as ApiFunction<Article, CreateArticleParams>,
    
    update: ((params: {id: number; data: Partial<CreateArticleParams>}) => 
      http.put(API_ROUTES.ADMIN.ARTICLE.UPDATE(params.id), params.data).then(r => r.data)) as ApiFunction<Article, {id: number; data: Partial<CreateArticleParams>}>,
    
    delete: ((id: number) => 
      http.delete(API_ROUTES.ADMIN.ARTICLE.DELETE(id)).then(r => r.data)) as ApiFunction<null, number>,
    
    setDraft: ((id: number) => 
      http.patch(API_ROUTES.ADMIN.ARTICLE.DRAFT(id)).then(r => r.data)) as ApiFunction<Article, number>,
    
    setPublish: ((id: number) => 
      http.patch(API_ROUTES.ADMIN.ARTICLE.PUBLISH(id)).then(r => r.data)) as ApiFunction<Article, number>
  }
}; 