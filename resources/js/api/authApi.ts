import http from './http';
import { refreshCsrfToken } from './http';
import { API_ROUTES } from './routes';
import { LoginParams, RegisterParams, AuthResponse, User } from '../types/auth';
import { ApiResponse, ApiFunction } from '../types/common';

export const authApi = {
  login: (async (params: LoginParams) => {
    // 登入前先刷新 CSRF token 確保有效性
    await refreshCsrfToken();
    const response = await http.post(API_ROUTES.AUTH.LOGIN, params);
    // 登入成功後不需要額外刷新 token，Laravel 的 session()->regenerate() 會處理
    return response.data;
  }) as ApiFunction<AuthResponse, LoginParams>,

  register: ((params: RegisterParams) => {
    return http.post(API_ROUTES.AUTH.REGISTER, params)
      .then(r => r.data);
  }) as ApiFunction<AuthResponse, RegisterParams>,

  logout: (() => {
    return http.post(API_ROUTES.AUTH.LOGOUT)
      .then(r => {
        // 登出成功後，重定向到登入頁以刷新整個應用程式狀態
        window.location.href = '/login';
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