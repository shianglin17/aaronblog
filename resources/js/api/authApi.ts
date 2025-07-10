import http from './http';
import { API_ROUTES } from './routes';
import { LoginParams, AuthResponse, User } from '../types/auth';
import { ApiResponse, ApiFunction } from '../types/common';

export const authApi = {
  login: ((params: LoginParams) => {
    return http.post(API_ROUTES.AUTH.LOGIN, params)
      .then(r => {
        // Session Cookie 方式不需要手動保存 token
        // Cookie 會自動由瀏覽器管理
        return r.data;
      });
  }) as ApiFunction<AuthResponse, LoginParams>,
  
  logout: (() => {
    return http.post(API_ROUTES.AUTH.LOGOUT)
      .then(r => {
        // Session Cookie 會由後端自動清除
        return r.data;
      });
  }) as ApiFunction<null>,
  
  // 獲取當前登入用戶
  getCurrentUser: (() => {
    return http.get(API_ROUTES.AUTH.USER).then(r => r.data);
  }) as ApiFunction<User>,
  
  // 檢查認證狀態（路由守衛專用，不觸發自動重定向）
  checkAuth: (() => {
    return http.get(API_ROUTES.AUTH.USER, {
      headers: { 'X-Skip-Auth-Redirect': 'true' }
    })
      .then(r => r.data)
      .catch(() => ({ status: 'error', message: '未登入' }));
  }) as ApiFunction<User>
}; 