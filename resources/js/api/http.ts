import axios from 'axios';
import { createDiscreteApi } from 'naive-ui';

// 創建 Naive UI 的獨立 API 實例，用於在非組件環境中使用
const { message } = createDiscreteApi(['message']);

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

/**
 * 顯示錯誤訊息
 * 處理不同類型的錯誤並顯示適當的提示
 */
function showErrorMessage(error: any): void {
  const data = error.response?.data;
  let errorMessage = '操作失敗，請稍後再試';
  
  if (data?.errors) {
    // 處理驗證錯誤：合併多條訊息
    const messages = Object.values(data.errors).flat() as string[];
    if (messages.length === 1) {
      errorMessage = messages[0];
    } else if (messages.length <= 3) {
      errorMessage = messages.join('；');
    } else {
      // 如果錯誤太多，只顯示前兩個並加上省略號
      errorMessage = `${messages.slice(0, 2).join('；')}... 等 ${messages.length} 個錯誤`;
    }
  } else if (data?.message) {
    // 單一錯誤訊息
    errorMessage = data.message;
  } else if (!error.response) {
    // 網路錯誤
    if (error.code === 'NETWORK_ERROR') {
      errorMessage = '網路連線錯誤，請檢查網路連線';
    } else if (error.code === 'ECONNABORTED') {
      errorMessage = '請求超時，請稍後再試';
    } else {
      errorMessage = '網路錯誤，請稍後再試';
    }
  }
  
  // 使用 Naive UI 的 message 顯示錯誤
  message.error(errorMessage);
}

// 響應攔截器 - 統一處理錯誤
http.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    // 先處理認證相關錯誤
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
    
    // 統一錯誤訊息處理
    showErrorMessage(error);
    
    return Promise.reject(error);
  }
);

export default http;