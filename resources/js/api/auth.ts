import http from './http';
import { API_ROUTES } from './routes';
import { LoginParams, AuthResponse } from '../types/auth';
import { ApiResponse } from '../types/common';

/**
 * 使用者登入
 * @param params 登入參數
 * @returns 登入結果
 */
export async function login(params: LoginParams): Promise<ApiResponse<AuthResponse>> {
  try {
    const response = await http.post(API_ROUTES.AUTH.LOGIN, params);
    
    // 如果登入成功，保存 token 和用戶資料
    if (response.data.status === 'success' && response.data.data.token) {
      localStorage.setItem('auth_token', response.data.data.token);
      
      // 保存用戶資料
      if (response.data.data.user) {
        localStorage.setItem('user_data', JSON.stringify(response.data.data.user));
      }
    }
    
    return response.data;
  } catch (error) {
    console.error('登入失敗:', error);
    throw error;
  }
}

/**
 * 使用者登出
 * @returns 登出結果
 */
export async function logout(): Promise<ApiResponse<null>> {
  try {
    const response = await http.post(API_ROUTES.AUTH.LOGOUT);
    
    // 登出時移除 token 和用戶資料
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    
    return response.data;
  } catch (error) {
    console.error('登出失敗:', error);
    
    // 即使 API 請求失敗，也清除本地 token 和用戶資料
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    
    throw error;
  }
}

/**
 * 檢查使用者是否已登入
 * @returns 是否已登入
 */
export function isLoggedIn(): boolean {
  return localStorage.getItem('auth_token') !== null;
} 