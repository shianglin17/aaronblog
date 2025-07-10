import axios from 'axios';

// 創建 axios 實例
const http = axios.create({
  baseURL: '/api', // API 基礎路徑
  timeout: 10000, // 請求超時時間
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
  },
  withCredentials: true // 啟用 Cookie 認證
});

// 請求攔截器 - 處理 CSRF Token
http.interceptors.request.use(
  async (config) => {
    // 避免對 CSRF cookie 端點的無限循環
    if (config.url !== '/sanctum/csrf-cookie' && 
        ['post', 'put', 'patch', 'delete'].includes(config.method?.toLowerCase() || '')) {
      try {
        // 先獲取 CSRF cookie
        await axios.get('/sanctum/csrf-cookie');
      } catch (error) {
        // 忽略 CSRF cookie 請求的錯誤
      }
    }
    
    // 獲取 CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // 如果有 CSRF Token，則將其添加到請求頭
    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken;
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
    // 統一處理錯誤
    if (error.response?.status === 401) {
      // 檢查是否為認證檢查請求，如果是則不重定向
      const isAuthCheck = error.config?.headers?.['X-Skip-Auth-Redirect'] === 'true';
      if (!isAuthCheck) {
        // 未授權，重定向到登入頁面
        console.log('未授權，請重新登入');
        window.location.href = '/login';
      }
    }
    
    return Promise.reject(error);
  }
);

export default http;