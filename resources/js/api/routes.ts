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
    CATEGORIES: '/categories'
  },
  TAG: {
    LIST: '/tags',
    DETAIL: (id: number) => `/tags/${id}`
  },
  ADMIN: {
    ARTICLE: {
      CREATE: '/admin/article',
      UPDATE: (id: number) => `/admin/article/${id}`,
      DELETE: (id: number) => `/admin/article/${id}`,
      DRAFT: (id: number) => `/admin/article/${id}/draft`,
      PUBLISH: (id: number) => `/admin/article/${id}/publish`
    },
    TAG: {
      CREATE: '/admin/tags',
      UPDATE: (id: number) => `/admin/tags/${id}`,
      DELETE: (id: number) => `/admin/tags/${id}`
    }
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
}; 