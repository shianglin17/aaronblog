import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '../api/index'
import type { User, AuthResponse, RegisterParams } from '../types/auth'
import type { ApiResponse } from '../types/common'

export const useAuthStore = defineStore('auth', () => {
  // 狀態
  const user = ref<User | null>(null)
  const isLoading = ref(false)
  
  // 計算屬性
  const isAuthenticated = computed(() => !!user.value)
  
  // 檢查認證狀態（帶緩存）
  const checkAuth = async (): Promise<boolean> => {
    // 如果已經有用戶資料，直接回傳認證狀態
    if (user.value) {
      return true
    }
    
    // 避免重複請求
    if (isLoading.value) {
      return false
    }
    
    try {
      isLoading.value = true
      const response: ApiResponse<User> = await authApi.checkAuth()
      
      if (response.status === 'success') {
        user.value = response.data
        return true
      } else {
        user.value = null
        return false
      }
    } catch (error) {
      user.value = null
      return false
    } finally {
      isLoading.value = false
    }
  }
  
  // 登入
  const login = async (email: string, password: string, remember: boolean = false) => {
    try {
      isLoading.value = true
      const response: ApiResponse<AuthResponse> = await authApi.login({ email, password, remember })
      
      if (response.status === 'success') {
        user.value = response.data.user
        return { success: true }
      } else {
        return { success: false, message: response.message }
      }
    } catch (error: any) {
      return { 
        success: false, 
        message: error.response?.data?.message || '登入失敗' 
      }
    } finally {
      isLoading.value = false
    }
  }
  
  // 註冊
  const register = async (params: RegisterParams) => {
    try {
      isLoading.value = true
      const response: ApiResponse<AuthResponse> = await authApi.register(params)
      
      if (response.status === 'success') {
        user.value = response.data.user
        return { success: true }
      } else {
        return { success: false, message: response.message }
      }
    } catch (error: any) {
      return { 
        success: false, 
        message: error.response?.data?.message || '註冊失敗' 
      }
    } finally {
      isLoading.value = false
    }
  }
  
  // 登出
  const logout = async () => {
    try {
      await authApi.logout();
      // API 內部會導向登入頁並刷新整個應用狀態
    } catch (error) {
      console.error('Logout API call failed:', error);
      user.value = null;
    }
  };
  
  return {
    // 狀態
    user,
    isLoading,
    
    // 計算屬性
    isAuthenticated,
    
    // 方法
    checkAuth,
    login,
    register,
    logout
  }
})
