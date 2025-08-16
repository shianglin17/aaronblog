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
  if (key === 'logout') {
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
.admin-layout {
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
@media (max-width: 768px) {
  .header-content {
    padding: 0 16px;
  }
  
  .admin-content {
    padding: 16px;
  }
  
  .content-wrapper {
    padding: 16px;
  }
  
  .user-name {
    display: none;
  }
}
</style> 