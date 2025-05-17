<template>
  <div class="admin-tag-container">
    <n-layout>
      <n-layout-header class="admin-header">
        <div class="header-content">
          <div class="logo">
            <h2>Aaron 標籤管理</h2>
          </div>
          <div class="user-actions">
            <n-space :size="20" align="center">
              <div class="nav-tabs">
                <n-button class="nav-tab" quaternary size="large" @click="$router.push('/admin/articles')">
                  文章管理
                </n-button>
                <n-button class="nav-tab active" quaternary size="large" @click="$router.push('/admin/tags')">
                  標籤管理
                </n-button>
                <n-button class="nav-tab" quaternary size="large" @click="$router.push('/admin/categories')">
                  分類管理
                </n-button>
              </div>
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
          <div class="page-header">
            <h1 class="page-title">標籤管理</h1>
            <n-space>
              <n-button type="primary" @click="openCreateModal">新增標籤</n-button>
              <n-input
                v-model:value="searchKeyword"
                placeholder="搜尋標籤名稱"
                clearable
                style="width: 200px"
                @keyup.enter="fetchTags"
              />
              <n-button @click="fetchTags">搜尋</n-button>
            </n-space>
          </div>
          <n-data-table
            :columns="columns"
            :data="tags"
            :loading="loading"
            :pagination="pagination"
            :row-key="row => row.id"
            style="margin-top: 24px"
            class="data-table"
            :empty-text="searchKeyword ? '沒有匹配的標籤' : '尚未創建任何標籤'"
          />
        </div>
        <!-- 新增/編輯標籤 Modal -->
        <n-modal v-model:show="showEditModal" preset="dialog" :title="editMode ? '編輯標籤' : '新增標籤'">
          <n-form :model="editForm" :rules="editRules" ref="editFormRef" label-width="80">
            <n-form-item label="名稱" path="name">
              <n-input v-model:value="editForm.name" maxlength="255" show-count />
            </n-form-item>
            <n-form-item label="Slug" path="slug">
              <n-input v-model:value="editForm.slug" maxlength="255" show-count />
            </n-form-item>
          </n-form>
          <template #action>
            <n-space>
              <n-button @click="showEditModal = false">取消</n-button>
              <n-button type="primary" @click="submitEditForm">{{ editMode ? '儲存變更' : '新增' }}</n-button>
            </n-space>
          </template>
        </n-modal>
      </n-layout-content>
    </n-layout>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, h, reactive } from 'vue';
import { useRouter } from 'vue-router';
import {
  NLayout,
  NLayoutHeader,
  NLayoutContent,
  NButton,
  NSpace,
  NInput,
  NDataTable,
  NPagination,
  NModal,
  NForm,
  NFormItem,
  NAvatar,
  NDropdown,
  NIcon,
  useMessage,
  DropdownOption
} from 'naive-ui';
import { ChevronDownOutline } from '@vicons/ionicons5';
import { authApi, tagApi } from '../../api/index';
import type { Tag, CreateTagParams, UpdateTagParams } from '../../types/tag';
import type { User } from '../../types/auth';

// 表單型別與 CreateTagParams 保持一致，但增加 id 欄位用於編輯
interface TagForm extends CreateTagParams {
  id: number | null;
}

const router = useRouter();
const message = useMessage();
const user = ref<User | null>(null);
const tags = ref<Tag[]>([]);
const loading = ref(false);
const showEditModal = ref(false);
const editMode = ref(false);
const editForm = reactive<TagForm>({
  id: null,
  name: '',
  slug: ''
});
const editFormRef = ref();
const editRules = {
  name: [{ required: true, message: '名稱不能為空', trigger: 'blur' }],
  slug: [{ required: true, message: 'Slug 不能為空', trigger: 'blur' }]
};
const searchKeyword = ref('');
const pagination = reactive({
  page: 1,
  pageSize: 10,
  showSizePicker: true,
  pageSizes: [10, 20, 30, 40],
  onChange: (page: number) => {
    pagination.page = page;
    fetchTags();
  },
  onUpdatePageSize: (pageSize: number) => {
    pagination.pageSize = pageSize;
    pagination.page = 1;
    fetchTags();
  }
});

const columns = [
  { 
    title: '名稱', 
    key: 'name',
    width: 200,
    ellipsis: {
      tooltip: true
    }
  },
  { 
    title: 'Slug', 
    key: 'slug',
    width: 200,
    ellipsis: {
      tooltip: true
    }
  },
  {
    title: '操作',
    key: 'actions',
    width: 150,
    align: 'center' as 'center',
    fixed: 'right' as 'right',
    render(row: Tag) {
      return h(NSpace, { justify: 'center' }, {
        default: () => [
          h(NButton, { size: 'small', onClick: () => openEditModal(row) }, { default: () => '編輯' }),
          h(NButton, { size: 'small', type: 'error', onClick: () => handleDelete(row) }, { default: () => '刪除' })
        ]
      });
    }
  }
];

function resetEditForm() {
  editForm.id = null;
  editForm.name = '';
  editForm.slug = '';
}

async function fetchTags() {
  loading.value = true;
  try {
    const res = await tagApi.getList();
    tags.value = res.data || [];
    // 如果有搜尋關鍵字，進行過濾
    if (searchKeyword.value) {
      tags.value = tags.value.filter(tag => 
        tag.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
      );
    }
  } catch (error) {
    message.error('取得標籤列表失敗');
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  resetEditForm();
  editMode.value = false;
  showEditModal.value = true;
}

function openEditModal(row: Tag) {
  Object.assign(editForm, {
    id: row.id,
    name: row.name,
    slug: row.slug
  });
  editMode.value = true;
  showEditModal.value = true;
}

async function submitEditForm() {
  // 表單驗證
  editFormRef.value?.validate(async (errors: any) => {
    if (errors) return;
    try {
      if (editMode.value && editForm.id) {
        await tagApi.update({
          id: editForm.id, 
          data: {
            name: editForm.name,
            slug: editForm.slug
          }
        });
        message.success('標籤更新成功');
      } else {
        await tagApi.create({
          name: editForm.name,
          slug: editForm.slug
        });
        message.success('標籤新增成功');
      }
      showEditModal.value = false;
      fetchTags();
    } catch (error) {
      message.error('儲存失敗，請檢查欄位或稍後再試');
    }
  });
}

async function handleDelete(row: Tag) {
  if (!confirm('確定要刪除這個標籤嗎？')) return;
  try {
    await tagApi.delete(row.id);
    message.success('刪除成功');
    fetchTags();
  } catch (error) {
    message.error('刪除失敗');
  }
}

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

// 用戶下拉菜單選項
const userMenuOptions = [
  {
    label: '登出',
    key: 'logout',
  }
];

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
  fetchTags();
});

async function handleLogout() {
  try {
    await authApi.logout();
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
.admin-tag-container {
  min-height: 100vh;
  background: #f5f7fa;
}
.admin-header {
  background: #f8f5f2;
  border-bottom: 1px solid #e0ddd7;
  padding: 0 24px;
}
.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 64px;
}
.logo h2 {
  margin: 0;
  font-size: 22px;
  color: #7d6e5d;
}
.user-actions {
  display: flex;
  align-items: center;
}
.admin-content {
  padding: 32px 24px;
}
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.page-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0;
}
.pagination-wrapper {
  margin-top: 24px;
  display: flex;
  justify-content: flex-end;
}
.nav-tabs {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid rgba(239, 239, 245, 0.35);
}
.nav-tab {
  position: relative;
  font-weight: 500;
  padding: 0 12px;
  height: 40px;
  border-radius: 4px 4px 0 0;
}
.nav-tab.active {
  color: #7d6e5d;
  background-color: rgba(239, 239, 245, 0.2);
}
.nav-tab.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #8f8072;
}
.data-table {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
</style> 