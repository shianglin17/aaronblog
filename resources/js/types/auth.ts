/**
 * 認證相關類型定義
 */

// 使用者登入請求參數
export interface LoginParams {
  email: string;
  password: string;
  remember?: boolean;
}

// 使用者註冊請求參數
export interface RegisterParams {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

// 使用者資料
export interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
  updated_at: string;
}

// 登入回應
export interface AuthResponse {
  user: User;
} 