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

/**
 * 獲取新的 CSRF token
 */
async function refreshCsrfToken(): Promise<string | null> {
  try {
    // 請求新的 CSRF token
    const response = await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
    
    // 這裡需要從 cookie 中讀取 XSRF-TOKEN
    const xsrfToken = document.cookie
      .split('; ')
      .find(row => row.startsWith('XSRF-TOKEN='))
      ?.split('=')[1];
    
    if (xsrfToken) {
      // 解碼 URL 編碼的 token
      return decodeURIComponent(xsrfToken);
    }
    return null;
  } catch (error) {
    console.warn('刷新 CSRF token 失敗:', error);
    return null;
  }
}

// 請求攔截器 - 處理 CSRF Token
http.interceptors.request.use(
  async (config) => {
    // 避免對 CSRF cookie 端點的無限循環
    if (config.url !== '/sanctum/csrf-cookie' && 
        ['post', 'put', 'patch', 'delete'].includes(config.method?.toLowerCase() || '')) {
      
      // 嘗試從 meta tag 獲取 CSRF token
      let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      // 如果沒有找到 token 或者是空的，嘗試刷新
      if (!csrfToken) {
        console.log('未找到 CSRF token，嘗試刷新...');
        csrfToken = await refreshCsrfToken();
        
        // 更新 meta tag
        if (csrfToken) {
          const metaTag = document.querySelector('meta[name="csrf-token"]');
          if (metaTag) {
            metaTag.setAttribute('content', csrfToken);
          }
        }
      }
      
      // 如果有 CSRF Token，則將其添加到請求頭
      if (csrfToken) {
        config.headers['X-CSRF-TOKEN'] = csrfToken;
      } else {
        console.warn('無法獲取 CSRF token，這可能會導致請求失敗');
      }
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
  async (error) => {
    // 統一處理錯誤
    if (error.response?.status === 401) {
      // 檢查是否為認證檢查請求，如果是則不重定向
      const isAuthCheck = error.config?.headers?.['X-Skip-Auth-Redirect'] === 'true';
      if (!isAuthCheck) {
        // 未授權，重定向到登入頁面
        console.log('未授權，請重新登入');
        window.location.href = '/login';
      }
    } else if (error.response?.status === 419) {
      // CSRF token 錯誤，嘗試刷新 token 並重試一次
      console.log('CSRF token 無效，嘗試刷新並重試...');
      
      try {
        const newToken = await refreshCsrfToken();
        if (newToken) {
          // 更新 meta tag
          const metaTag = document.querySelector('meta[name="csrf-token"]');
          if (metaTag) {
            metaTag.setAttribute('content', newToken);
          }
          
          // 更新原始請求的 CSRF token 並重試
          if (error.config) {
            error.config.headers['X-CSRF-TOKEN'] = newToken;
            return http.request(error.config);
          }
        }
      } catch (retryError) {
        console.error('重試請求失敗:', retryError);
      }
    }
    
    return Promise.reject(error);
  }
);

export default http;