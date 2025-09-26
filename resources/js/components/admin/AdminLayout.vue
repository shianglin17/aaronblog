<template>
    <div class="admin-layout">
      <n-layout>
        <n-layout-header class="admin-header">
          <div class="header-content">
            <div class="logo">
              <h2>Aaron 內容管理</h2>
            </div>
            <div class="user-actions">
              <n-space :size="20" align="center">
                <div class="nav-tabs">
                  <n-button 
                    class="nav-tab" 
                    :class="{ active: currentRoute === '/admin/articles' }"
                    quaternary 
                    size="large" 
                    @click="$router.push('/admin/articles')"
                  >
                    文章管理
                  </n-button>
                  <n-button 
                    class="nav-tab" 
                    :class="{ active: currentRoute === '/admin/tags' }"
                    quaternary 
                    size="large" 
                    @click="$router.push('/admin/tags')"
                  >
                    標籤管理
                  </n-button>
                  <n-button 
                    class="nav-tab" 
                    :class="{ active: currentRoute === '/admin/categories' }"
                    quaternary 
                    size="large" 
                    @click="$router.push('/admin/categories')"
                  >
                    分類管理
                  </n-button>
                </div>
                <n-dropdown 
                  :options="userMenuOptions" 
                  @select="handleUserMenuSelect"
                  trigger="click"
                >
                  <n-button quaternary>
                    <n-avatar 
                      round 
                      size="small" 
                      :src="userAvatar"
                      color="#8f8072"
                    >{{ userInitials }}</n-avatar>
                    <span class="user-name">{{ authStore.user ? authStore.user.name : '管理員' }}</span>
                    <n-icon><chevron-down-outline /></n-icon>
                  </n-button>
                </n-dropdown>
              </n-space>
            </div>
          </div>
        </n-layout-header>
        <n-layout-content class="admin-content">
          <div class="content-wrapper">
            <slot></slot>
          </div>
        </n-layout-content>
      </n-layout>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useMessage } from 'naive-ui';
import { ChevronDownOutline } from '@vicons/ionicons5';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const route = useRoute();
const message = useMessage();
const authStore = useAuthStore();

// 當前路由
const currentRoute = computed(() => route.path);

// 用戶菜單選項
const userMenuOptions = computed(() => [
  {
    key: 'frontend',
    label: '回到前台'
  },
  {
    key: 'logout',
    label: '登出'
  }
]);

// 用戶頭像
const userAvatar = computed(() => {
  return '';  // 暫不支持頭像功能
});

// 用戶名稱首字母
const userInitials = computed(() => {
  if (!authStore.user || !authStore.user.name) return '';
  return authStore.user.name.charAt(0).toUpperCase();
});

// 處理用戶菜單選擇
async function handleUserMenuSelect(key: string) {
  if (key === 'frontend') {
    // 跳轉到實際的前台首頁（Blade渲染）
    window.location.href = '/';
  } else if (key === 'logout') {
    try {
      await authStore.logout();
      message.success('登出成功');
      // 登出後重新載入頁面以獲取新的 CSRF token，避免後續登入時的 CSRF 錯誤
      window.location.href = '/login';
    } catch (error) {
      message.error('登出失敗');
      console.error(error);
    }
  }
}
</script>

<style scoped>
/* 後台專用 CSS 變數 - 專業灰藍色系設計系統 */
.admin-layout {
  /* 基礎色彩 - 專業管理系統色調 */
  --background-color: #f0f2f5;
  --text-color: #262626;
  --text-secondary: #595959;
  --text-tertiary: #8c8c8c;
  --border-color: #d9d9d9;
  
  /* 品牌色彩系統 - 專業藍色系 */
  --brand-primary: #1890ff;
  --brand-secondary: #40a9ff;
  --brand-tertiary: #69c0ff;
  --brand-gradient: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 60%, var(--brand-tertiary) 100%);
  --brand-light: #f0f8ff;
  
  /* 語義色彩 - 管理系統標準 */
  --success-color: #52c41a;
  --success-light: #f6ffed;
  --warning-color: #faad14;
  --warning-light: #fffbe6;
  --error-color: #ff4d4f;
  --error-light: #fff2f0;
  --info-color: #1890ff;
  --info-light: #e6f7ff;
  
  /* 表面層次系統 - 管理界面層級 */
  --surface-color: #ffffff;
  --surface-elevated: #ffffff;
  --surface-hover: #fafafa;
  --surface-active: #f5f5f5;
  --surface-secondary: #fafafa;
  --surface-tertiary: #f0f0f0;
  
  /* 陰影系統 - 管理系統標準陰影 */
  --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  
  /* 互動狀態 - 管理系統標準 */
  --hover-opacity: 0.85;
  --active-scale: 0.98;
  --transition-fast: 0.1s ease-out;
  --transition-normal: 0.15s ease-out;
  --transition-slow: 0.2s ease-out;
  
  /* 管理界面特定變數 */
  --admin-header-bg: #ffffff;
  --admin-header-height: 64px;
  --admin-header-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
  --admin-content-bg: #f0f2f5;
  --admin-content-padding: 24px;
  
  /* 表格變數 */
  --table-header-bg: #fafafa;
  --table-border-color: #f0f0f0;
  --table-hover-bg: #fafafa;
  --table-selected-bg: #e6f7ff;
  
  /* 表單變數 */
  --form-label-color: #262626;
  --form-input-bg: #ffffff;
  --form-input-border: #d9d9d9;
  --form-input-focus-border: #40a9ff;
  
  /* 卡片變數 - 管理系統風格 */
  --card-background: #ffffff;
  --card-border-radius: 8px;
  --card-padding: 24px;
  --card-margin: 16px;
  --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  --card-border: 1px solid #f0f0f0;
  
  /* 按鈕變數 */
  --button-border-radius: 6px;
  --button-primary-bg: var(--brand-primary);
  --button-primary-hover: var(--brand-secondary);
  --button-default-bg: #ffffff;
  --button-default-border: #d9d9d9;
  
  /* 導航變數 */
  --nav-active-bg: #e6f7ff;
  --nav-active-color: var(--brand-primary);
  --nav-hover-bg: #f5f5f5;
  
  /* 狀態標籤變數 */
  --status-published-bg: #f6ffed;
  --status-published-color: #52c41a;
  --status-draft-bg: #fff7e6;
  --status-draft-color: #faad14;
  --status-archived-bg: #f5f5f5;
  --status-archived-color: #8c8c8c;
  
  /* RWD 斷點變數 */
  --breakpoint-xs: 480px;   /* 超小螢幕：手機直式 */
  --breakpoint-sm: 576px;   /* 小螢幕：手機橫式 */
  --breakpoint-md: 768px;   /* 中等螢幕：平板直式 */
  --breakpoint-lg: 992px;   /* 大螢幕：平板橫式/小筆電 */
  --breakpoint-xl: 1200px;  /* 超大螢幕：桌機 */
  --breakpoint-xxl: 1600px; /* 極大螢幕：大桌機 */
  
  /* 移動端特定變數 */
  --mobile-header-height: 56px;
  --mobile-padding: 12px;
  --mobile-content-padding: 16px;
  --mobile-card-padding: 16px;
  --mobile-font-size-sm: 0.75rem;
  --mobile-font-size-base: 0.875rem;
  --mobile-font-size-lg: 1rem;
  
  /* 平板特定變數 */
  --tablet-header-height: 60px;
  --tablet-padding: 16px;
  --tablet-content-padding: 20px;
  --tablet-card-padding: 20px;
  
  /* 響應式表格變數 */
  --table-min-width: 800px;
  --table-mobile-min-width: 320px;
  --table-scroll-width: 100%;
  
  min-height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
}

.admin-header {
  background-color: var(--admin-header-bg);
  box-shadow: var(--admin-header-shadow);
  position: sticky;
  top: 0;
  z-index: 100;
  height: var(--admin-header-height);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 var(--admin-content-padding);
  height: 100%;
}

.logo h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 500;
  color: var(--text-color);
}

.user-actions {
  display: flex;
  align-items: center;
}

.user-name {
  margin: 0 8px;
}

.admin-content {
  padding: var(--admin-content-padding);
  background-color: var(--admin-content-bg);
  min-height: calc(100vh - var(--admin-header-height));
}

.content-wrapper {
  background-color: var(--card-background);
  border-radius: var(--card-border-radius);
  padding: var(--card-padding);
  box-shadow: var(--card-shadow);
}

.nav-tabs {
  display: flex;
  gap: 8px;
}

.nav-tab {
  position: relative;
}

.nav-tab.active::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: var(--brand-primary);
}

/* 響應式設計 */

/* 極小螢幕：手機直式 (< 480px) */
@media (max-width: 479px) {
  .admin-layout {
    min-height: 100vh;
  }
  
  .admin-header {
    height: var(--mobile-header-height);
  }
  
  .header-content {
    padding: 0 var(--mobile-padding);
    height: 100%;
  }
  
  .logo h2 {
    font-size: var(--mobile-font-size-lg);
    font-weight: 600;
  }
  
  .nav-tabs {
    display: none; /* 隱藏導航標籤，改用下拉菜單 */
  }
  
  .user-name {
    display: none;
  }
  
  .admin-content {
    padding: var(--mobile-content-padding);
    min-height: calc(100vh - var(--mobile-header-height));
  }
  
  .content-wrapper {
    padding: var(--mobile-card-padding);
    border-radius: 4px;
  }
}

/* 小螢幕：手機橫式 (480px - 575px) */
@media (min-width: 480px) and (max-width: 575px) {
  .admin-header {
    height: var(--mobile-header-height);
  }
  
  .header-content {
    padding: 0 var(--mobile-content-padding);
  }
  
  .logo h2 {
    font-size: var(--mobile-font-size-lg);
  }
  
  .nav-tabs {
    gap: 4px;
  }
  
  .nav-tabs .n-button {
    font-size: var(--mobile-font-size-sm);
    padding: 4px 8px;
  }
  
  .user-name {
    display: none;
  }
  
  .admin-content {
    padding: var(--mobile-content-padding);
    min-height: calc(100vh - var(--mobile-header-height));
  }
  
  .content-wrapper {
    padding: var(--mobile-card-padding);
  }
}

/* 中等螢幕：平板直式 (576px - 767px) */
@media (min-width: 576px) and (max-width: 767px) {
  .admin-header {
    height: var(--tablet-header-height);
  }
  
  .header-content {
    padding: 0 var(--tablet-padding);
  }
  
  .nav-tabs {
    gap: 6px;
  }
  
  .nav-tabs .n-button {
    font-size: 0.875rem;
    padding: 6px 12px;
  }
  
  .user-name {
    display: none;
  }
  
  .admin-content {
    padding: var(--tablet-content-padding);
    min-height: calc(100vh - var(--tablet-header-height));
  }
  
  .content-wrapper {
    padding: var(--tablet-card-padding);
  }
}

/* 大螢幕：平板橫式 (768px - 991px) */
@media (min-width: 768px) and (max-width: 991px) {
  .header-content {
    padding: 0 20px;
  }
  
  .nav-tabs {
    gap: 8px;
  }
  
  .user-name {
    font-size: 0.875rem;
  }
  
  .admin-content {
    padding: 20px;
  }
  
  .content-wrapper {
    padding: 20px;
  }
}

/* 超大螢幕：桌機 (> 1200px) */
@media (min-width: 1200px) {
  .header-content {
    padding: 0 32px;
  }
  
  .admin-content {
    padding: 32px;
  }
  
  .content-wrapper {
    padding: 32px;
  }
  
  .nav-tabs {
    gap: 12px;
  }
}
</style> 