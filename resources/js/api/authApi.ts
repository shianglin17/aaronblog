import http from './http';
import { API_ROUTES } from './routes';
import { LoginParams, AuthResponse, User } from '../types/auth';
import { ApiResponse, ApiFunction } from '../types/common';

export const authApi = {
  login: ((params: LoginParams) => {
    return http.post(API_ROUTES.AUTH.LOGIN, params)
      .then(r => {
        // 如果登入成功，保存 token 和用戶資料
        if (r.data.status === 'success' && r.data.data.token) {
          localStorage.setItem('auth_token', r.data.data.token);
          
          // 保存用戶資料
          if (r.data.data.user) {
            localStorage.setItem('user_data', JSON.stringify(r.data.data.user));
          }
        }
        return r.data;
      });
  }) as ApiFunction<AuthResponse, LoginParams>,
  
  logout: (() => {
    return http.post(API_ROUTES.AUTH.LOGOUT)
      .then(r => {
        // 登出時移除 token 和用戶資料
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        return r.data;
      })
      .catch(error => {
        // 即使 API 請求失敗，也清除本地 token 和用戶資料
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        throw error;
      });
  }) as ApiFunction<null>,
  
  // 獲取當前登入用戶
  getCurrentUser: (() => {
    // 首先嘗試從本地存儲獲取
    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
      const userData = JSON.parse(userDataStr);
      return Promise.resolve({
        status: 'success',
        code: 200,
        message: 'User data retrieved from local storage',
        data: userData
      });
    }
    
    // 如果本地沒有，從 API 獲取
    return http.get(API_ROUTES.USER.PROFILE)
      .then(r => {
        if (r.data.status === 'success' && r.data.data) {
          localStorage.setItem('user_data', JSON.stringify(r.data.data));
        }
        return r.data;
      });
  }) as ApiFunction<User>,
  
  // 本地功能，非 API 呼叫
  isLoggedIn: (): boolean => {
    return localStorage.getItem('auth_token') !== null;
  }
}; 