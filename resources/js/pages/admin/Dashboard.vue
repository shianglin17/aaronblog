<template>
  <div class="admin-container">
    <n-layout>
      <n-layout-header class="admin-header">
        <div class="header-content">
          <div class="logo">
            <h2>Aaron 後台管理</h2>
          </div>
          <div class="user-actions">
            <n-dropdown 
              :options="userMenuOptions" 
              @select="handleUserMenuSelect"
              :render-label="renderUserLabel"
              trigger="click"
            >
              <n-button quaternary>
                <n-avatar 
                  round 
                  size="small" 
                  :src="userAvatar"
                  color="#3498db"
                >{{ userInitials }}</n-avatar>
                <span class="user-name">{{ user ? user.name : '管理員' }}</span>
                <n-icon><chevron-down-outline /></n-icon>
              </n-button>
            </n-dropdown>
          </div>
        </div>
      </n-layout-header>
      
      <n-layout has-sider>
        <n-layout-sider
          collapse-mode="width"
          :collapsed-width="64"
          :width="240"
          :native-scrollbar="false"
          class="admin-sider"
          bordered
        >
          <n-menu
            :collapsed-width="64"
            :collapsed-icon-size="22"
            :options="menuOptions"
            default-value="dashboard"
          />
        </n-layout-sider>
        
        <n-layout-content class="admin-content">
          <div class="content-wrapper">
            <div class="page-header">
              <h1 class="page-title">儀表板</h1>
              <p class="page-subtitle">網站總覽</p>
            </div>
            
            <n-grid :cols="24" :x-gap="24" :y-gap="24">
              <n-grid-item :span="24" :md="12">
                <n-card title="網站概況" size="medium">
                  <n-space vertical>
                    <div class="welcome-message">
                      <p class="welcome-text">歡迎回來，{{ user ? user.name : '管理員' }}！</p>
                      <p>這是管理員儀表板頁面。您可以在這裡查看網站總覽數據。</p>
                    </div>
                  </n-space>
                </n-card>
              </n-grid-item>
              
              <n-grid-item :span="12" :md="6">
                <n-card class="stat-card">
                  <n-statistic label="文章總數" :value="0">
                    <template #prefix>
                      <n-icon size="24" color="#3498db">
                        <document-text-outline />
                      </n-icon>
                    </template>
                  </n-statistic>
                </n-card>
              </n-grid-item>
              
              <n-grid-item :span="12" :md="6">
                <n-card class="stat-card">
                  <n-statistic label="用戶總數" :value="1">
                    <template #prefix>
                      <n-icon size="24" color="#e74c3c">
                        <people-outline />
                      </n-icon>
                    </template>
                  </n-statistic>
                </n-card>
              </n-grid-item>
              
              <n-grid-item :span="24">
                <n-card title="最近活動" size="medium">
                  <n-empty description="暫無活動記錄" />
                </n-card>
              </n-grid-item>
            </n-grid>
          </div>
        </n-layout-content>
      </n-layout>
    </n-layout>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, h } from 'vue';
import { useRouter } from 'vue-router';
import { 
  NLayout,
  NLayoutHeader,
  NLayoutContent,
  NLayoutSider,
  NMenu,
  NCard,
  NButton, 
  NSpace, 
  NGrid, 
  NGridItem, 
  NStatistic, 
  NIcon,
  NAvatar,
  NDropdown,
  NEmpty,
  DropdownOption
} from 'naive-ui';
import { 
  DocumentTextOutline, 
  PeopleOutline, 
  HomeOutline,
  NewspaperOutline,
  SettingsOutline,
  LogOutOutline,
  PersonOutline,
  ChevronDownOutline
} from '@vicons/ionicons5';
import { logout } from '../../api/auth';
import type { User } from '../../types/auth';

const router = useRouter();
const user = ref<User | null>(null);

// 用戶頭像處理
const userAvatar = computed<string | undefined>(() => {
  // 如果用戶有頭像，顯示頭像，否則顯示文字頭像
  return undefined;
});

// 用戶名稱首字母
const userInitials = computed(() => {
  if (user.value && user.value.name) {
    return user.value.name.charAt(0).toUpperCase();
  }
  return 'A';
});

// 菜單選項
const menuOptions = [
  {
    label: '儀表板',
    key: 'dashboard',
    icon: renderIcon(HomeOutline)
  },
  {
    label: '文章管理',
    key: 'articles',
    icon: renderIcon(NewspaperOutline),
    children: [
      {
        label: '所有文章',
        key: 'articles-list'
      },
      {
        label: '新增文章',
        key: 'articles-create'
      }
    ]
  },
  {
    label: '設定',
    key: 'settings',
    icon: renderIcon(SettingsOutline)
  }
];

// 用戶下拉菜單選項
const userMenuOptions = [
  {
    label: '個人資料',
    key: 'profile',
    icon: renderIcon(PersonOutline)
  },
  {
    label: '登出',
    key: 'logout',
    icon: renderIcon(LogOutOutline)
  }
];

// 渲染圖標
function renderIcon(icon: any) {
  return () => h(NIcon, null, { default: () => h(icon) });
}

// 渲染用戶標籤
function renderUserLabel(option: DropdownOption) {
  return h(
    'div',
    {
      style: {
        display: 'flex',
        alignItems: 'center',
      },
    },
    [
      h(
        NIcon,
        {
          style: {
            marginRight: '8px',
          },
        },
        { default: () => h(option.icon as any) }
      ),
      option.label as string
    ]
  );
}

// 處理用戶菜單選擇
function handleUserMenuSelect(key: string) {
  if (key === 'logout') {
    handleLogout();
  } else if (key === 'profile') {
    // 導航到個人資料頁面
    // router.push('/admin/profile');
  }
}

onMounted(() => {
  // 從本地儲存的資料中獲取用戶信息
  const userDataStr = localStorage.getItem('user_data');
  if (userDataStr) {
    try {
      user.value = JSON.parse(userDataStr);
    } catch (error) {
      console.error('解析用戶資料時出錯:', error);
    }
  }
});

async function handleLogout() {
  try {
    await logout();
    // 登出成功，導向首頁
    router.push('/');
  } catch (error) {
    console.error('登出時發生錯誤:', error);
    // 即使API請求失敗，也應清除本地存儲並導向登入頁面
    router.push('/login');
  }
}
</script>

<style scoped>
.admin-container {
  min-height: 100vh;
  background-color: var(--body-bg, #f5f7f9);
}

.admin-header {
  height: 64px;
  padding: 0 24px;
  background-color: #fff;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 999;
}

.header-content {
  height: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 500;
  color: var(--text-color, #333);
}

.user-actions {
  display: flex;
  align-items: center;
}

.user-name {
  margin: 0 8px;
}

.admin-sider {
  position: fixed;
  top: 64px;
  left: 0;
  height: calc(100vh - 64px);
  overflow: hidden;
  z-index: 998;
}

.admin-content {
  margin-top: 64px;
  margin-left: 240px;
  padding: 24px;
  min-height: calc(100vh - 64px);
}

.content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 24px;
}

.page-title {
  margin: 0 0 8px 0;
  font-size: 1.75rem;
  font-weight: 500;
  color: var(--text-color, #333);
}

.page-subtitle {
  margin: 0;
  font-size: 0.9rem;
  color: var(--text-secondary, #666);
}

.welcome-message {
  margin-bottom: 1rem;
}

.welcome-text {
  font-size: 1.2rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.stat-card {
  height: 100%;
}

/* 響應式設計 */
@media (max-width: 992px) {
  .admin-content {
    margin-left: 64px;
    padding: 16px;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: 1.5rem;
  }
}
</style> 