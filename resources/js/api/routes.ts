/**
 * 集中管理 API 路由
 * 統一定義所有後端 API 路徑，避免路徑分散
 */

export const API_ROUTES = {
  ARTICLE: {
    LIST: '/article/list',
    DETAIL: (id: number) => `/article/${id}`,
    CREATE: '/article',
    UPDATE: (id: number) => `/article/${id}`,
    DELETE: (id: number) => `/article/${id}`,
    CATEGORIES: '/categories',
    TAGS: '/tags'
  },
  AUTH: {
    LOGIN: '/auth/login',
    LOGOUT: '/auth/logout',
    REGISTER: '/auth/register'
  },
  USER: {
    PROFILE: '/user/profile',
    UPDATE_PROFILE: '/user/profile'
  }
  // 其他 API 路由可在此擴展...
}; 