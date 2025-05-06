import axios from 'axios';

// 創建 axios 實例
const http = axios.create({
  baseURL: '/api', // API 基礎路徑
  timeout: 10000, // 請求超時時間
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
  }
});

// 請求攔截器 - 處理授權等
http.interceptors.request.use(
  (config) => {
    // 從本地存儲獲取 token
    const token = localStorage.getItem('auth_token');
    
    // 如果有 token，則將其添加到請求頭
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// 響應攔截器 - 統一處理錯誤
http.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // 可以在這裡統一處理錯誤，如未授權(401)、禁止訪問(403)等
    if (error.response) {
      // 处理响应错误
      if (error.response.status === 401) {
        // 未授权，可以执行登出或跳转到登录页
        console.log('未授權，請重新登入');
        // 可以觸發登出操作或跳轉
      }
    }
    
    return Promise.reject(error);
  }
);

export default http;