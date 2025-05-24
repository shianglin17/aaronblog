<template>
    <div class="admin-container">
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
                    <span class="user-name">{{ user ? user.name : '管理員' }}</span>
                    <n-icon><chevron-down-outline /></n-icon>
                  </n-button>
                </n-dropdown>
              </n-space>
            </div>
          </div>
        </n-layout-header>
        <n-layout-content class="admin-content">
          <div class="content-wrapper">
            <!-- 頁面內容 -->
            <slot></slot>
          </div>
        </n-layout-content>
      </n-layout>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useMessage } from 'naive-ui';
import { ChevronDownOutline } from '@vicons/ionicons5';
import { authApi } from '../../api/index';
import type { User } from '../../types/auth';

const router = useRouter();
const route = useRoute();
const message = useMessage();
const user = ref<User | null>(null);

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
  // 從本地存儲獲取用戶數據
  const userDataStr = localStorage.getItem('user_data');
  if (!userDataStr) return '';
  
  try {
    const userData = JSON.parse(userDataStr);
    return userData.name.charAt(0).toUpperCase();
  } catch (error) {
    console.error('解析用戶數據失敗', error);
    return '';
  }
});

// 處理用戶菜單選擇
async function handleUserMenuSelect(key: string) {
  if (key === 'logout') {
    try {
      await authApi.logout();
      message.success('登出成功');
      router.push('/login');
    } catch (error) {
      message.error('登出失敗');
      console.error(error);
    }
  }
}

// 獲取當前用戶資訊
function fetchUserInfo() {
  const userDataStr = localStorage.getItem('user_data');
  if (userDataStr) {
    try {
      user.value = JSON.parse(userDataStr);
    } catch (error) {
      console.error('解析用戶數據失敗', error);
    }
  }
}

// 組件掛載時獲取用戶資訊
onMounted(() => {
  fetchUserInfo();
});
</script>

<style scoped>
.admin-container {
  min-height: 100vh;
}

.admin-header {
  background-color: #fff;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 24px;
  height: 64px;
}

.logo h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 500;
  color: #333;
}

.user-actions {
  display: flex;
  align-items: center;
}

.user-name {
  margin: 0 8px;
}

.admin-content {
  padding: 24px;
  background-color: #f5f5f5;
  min-height: calc(100vh - 64px);
}

.content-wrapper {
  background-color: #fff;
  border-radius: 4px;
  padding: 24px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
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
  background-color: var(--primary-color);
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