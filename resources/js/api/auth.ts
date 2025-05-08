import http from './http';
import { API_ROUTES } from './routes';
import { LoginParams, RegisterParams, AuthResponse } from '../types/auth';
import { ApiResponse } from '../types/common';

/**
 * 使用者登入
 * @param params 登入參數
 * @returns 登入結果
 */
export async function login(params: LoginParams): Promise<ApiResponse<AuthResponse>> {
  try {
    const response = await http.post(API_ROUTES.AUTH.LOGIN, params);
    
    // 如果登入成功，保存 token
    if (response.data.status === 'success' && response.data.data.token) {
      localStorage.setItem('auth_token', response.data.data.token);
    }
    
    return response.data;
  } catch (error) {
    console.error('登入失敗:', error);
    throw error;
  }
}

/**
 * 使用者註冊
 * @param params 註冊參數
 * @returns 註冊結果
 */
export async function register(params: RegisterParams): Promise<ApiResponse<AuthResponse>> {
  try {
    const response = await http.post(API_ROUTES.AUTH.REGISTER, params);
    
    // 如果註冊成功並直接登入，保存 token
    if (response.data.status === 'success' && response.data.data.token) {
      localStorage.setItem('auth_token', response.data.data.token);
    }
    
    return response.data;
  } catch (error) {
    console.error('註冊失敗:', error);
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
    
    // 登出時移除 token
    localStorage.removeItem('auth_token');
    
    return response.data;
  } catch (error) {
    console.error('登出失敗:', error);
    
    // 即使 API 請求失敗，也清除本地 token
    localStorage.removeItem('auth_token');
    
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