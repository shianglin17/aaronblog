/**
 * 集中管理 API 路由
 * 統一定義所有後端 API 路徑，避免路徑分散
 */

export const API_ROUTES = {
  ARTICLE: {
    LIST: '/articles',
    DETAIL: (id: number) => `/articles/${id}`,
    CREATE: '/articles',
    UPDATE: (id: number) => `/articles/${id}`,
    DELETE: (id: number) => `/articles/${id}`,
    CATEGORIES: '/categories'
  },
  TAG: {
    LIST: '/tags',
    DETAIL: (id: number) => `/tags/${id}`
  },
  CATEGORY: {
    LIST: '/categories',
    DETAIL: (id: number) => `/categories/${id}`
  },
  ADMIN: {
    ARTICLE: {
      LIST: '/admin/articles',
      CREATE: '/admin/articles',
      UPDATE: (id: number) => `/admin/articles/${id}`,
      DELETE: (id: number) => `/admin/articles/${id}`
    },
    TAG: {
      CREATE: '/admin/tags',
      UPDATE: (id: number) => `/admin/tags/${id}`,
      DELETE: (id: number) => `/admin/tags/${id}`
    },
    CATEGORY: {
      CREATE: '/admin/categories',
      UPDATE: (id: number) => `/admin/categories/${id}`,
      DELETE: (id: number) => `/admin/categories/${id}`
    }
  },
  AUTH: {
    LOGIN: '/auth/login',
    LOGOUT: '/auth/logout',
    REGISTER: '/auth/register',
    USER: '/auth/user'
  },
  USER: {
    PROFILE: '/user/profile',
    UPDATE_PROFILE: '/user/profile'
  }
}; 