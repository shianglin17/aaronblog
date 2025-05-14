<template>
  <div class="admin-article-container">
    <n-layout>
      <n-layout-header class="admin-header">
        <div class="header-content">
          <div class="logo">
            <h2>Aaron 文章管理</h2>
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
                  color="#8f8072"
                >{{ userInitials }}</n-avatar>
                <span class="user-name">{{ user ? user.name : '管理員' }}</span>
                <n-icon><chevron-down-outline /></n-icon>
              </n-button>
            </n-dropdown>
          </div>
        </div>
      </n-layout-header>
      <n-layout-content class="admin-content">
        <div class="content-wrapper">
          <div class="page-header">
            <h1 class="page-title">文章管理</h1>
            <n-space>
              <n-button type="primary" @click="openCreateModal">新增文章</n-button>
              <n-select
                v-model:value="statusFilter"
                :options="statusOptions"
                placeholder="全部狀態"
                style="width: 120px"
                @update:value="fetchArticles"
              />
              <n-select
                v-model:value="categoryFilter"
                :options="categoryOptions"
                placeholder="全部分類"
                style="width: 140px"
                clearable
              />
              <n-select
                v-model:value="tagFilter"
                :options="tagOptions"
                placeholder="全部標籤"
                style="width: 180px"
                multiple
                clearable
              />
              <n-input
                v-model:value="searchKeyword"
                placeholder="搜尋標題/內容"
                clearable
                style="width: 200px"
                @keyup.enter="fetchArticles"
              />
              <n-button @click="fetchArticles">搜尋</n-button>
            </n-space>
          </div>
          <n-data-table
            :columns="columns"
            :data="articles"
            :loading="loading"
            :pagination="false"
            :row-key="row => row.id"
            style="margin-top: 24px"
          />
          <div class="pagination-wrapper">
            <n-pagination
              v-model:page="pagination.currentPage"
              :page-size="pagination.perPage"
              :item-count="pagination.totalItems"
              @update:page="fetchArticles"
            />
          </div>
        </div>
        <!-- 新增/編輯文章 Modal -->
        <n-modal v-model:show="showEditModal" preset="dialog" :title="editMode ? '編輯文章' : '新增文章'">
          <n-form :model="editForm" :rules="editRules" ref="editFormRef" label-width="80">
            <n-form-item label="標題" path="title">
              <n-input v-model:value="editForm.title" maxlength="255" show-count />
            </n-form-item>
            <n-form-item label="Slug" path="slug">
              <n-input v-model:value="editForm.slug" maxlength="255" show-count />
            </n-form-item>
            <n-form-item label="摘要" path="description">
              <n-input v-model:value="editForm.description" maxlength="160" show-count type="textarea" />
            </n-form-item>
            <n-form-item label="內容" path="content">
              <n-input v-model:value="editForm.content" type="textarea" rows="6" />
            </n-form-item>
            <n-form-item label="分類" path="category_id">
              <n-select v-model:value="editForm.category_id" :options="categoryOptions" clearable />
            </n-form-item>
            <n-form-item label="狀態" path="status">
              <n-select v-model:value="editForm.status" :options="statusOptions" />
            </n-form-item>
            <n-form-item label="標籤" path="tags">
              <n-select v-model:value="editForm.tags" :options="tagOptions" multiple clearable />
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
  NSelect,
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
import {
  getArticleList,
  getAllCategories,
  getAllTags,
  getArticleById
} from '../../api/article';
import {
  createArticle,
  updateArticle,
  deleteArticle,
  setArticleDraft,
  setArticlePublish
} from '../../api/articleadmin';
import { logout } from '../../api/auth';
import type { User } from '../../types/auth';
import type { Article, Category, Tag } from '../../types/article';

interface ArticleForm {
  id: number | null;
  title: string;
  slug: string;
  description: string;
  content: string;
  category_id: number | null;
  status: 'draft' | 'published';
  tags: number[];
}

const router = useRouter();
const message = useMessage();
const user = ref<User | null>(null);
const articles = ref<Article[]>([]);
const loading = ref(false);
const showEditModal = ref(false);
const editMode = ref(false);
const editForm = reactive<ArticleForm>({
  id: null,
  title: '',
  slug: '',
  description: '',
  content: '',
  category_id: null,
  status: 'draft',
  tags: []
});
const editFormRef = ref();
const editRules = {
  title: [{ required: true, message: '標題不能為空', trigger: 'blur' }],
  slug: [{ required: true, message: 'Slug 不能為空', trigger: 'blur' }],
  description: [{ required: true, message: '摘要不能為空', trigger: 'blur' }],
  content: [{ required: true, message: '內容不能為空', trigger: 'blur' }]
};
const statusFilter = ref('');
const categoryFilter = ref<string | null>(null);
const tagFilter = ref<string[]>([]);
const statusOptions = [
  { label: '全部', value: '' },
  { label: '草稿', value: 'draft' },
  { label: '已發佈', value: 'published' }
];
const searchKeyword = ref('');
const pagination = reactive({
  currentPage: 1,
  totalPages: 1,
  totalItems: 0,
  perPage: 15
});
const categoryOptions = ref<{ label: string, value: string }[]>([]);
const tagOptions = ref<{ label: string, value: string }[]>([]);

const columns = [
  { title: '標題', key: 'title' },
  { title: '狀態', key: 'status', render(row: any) { return row.status === 'published' ? '已發佈' : '草稿'; } },
  { title: '分類', key: 'category', render(row: any) { return row.category?.name || '-'; } },
  { title: '作者', key: 'author', render(row: any) { return row.author?.name || '-'; } },
  { title: '建立時間', key: 'created_at' },
  { title: '更新時間', key: 'updated_at' },
  {
    title: '操作',
    key: 'actions',
    render(row: any) {
      return h(NSpace, {}, {
        default: () => [
          h(NButton, { size: 'small', onClick: () => openEditModal(row) }, { default: () => '編輯' }),
          h(NButton, { size: 'small', type: 'error', onClick: () => handleDelete(row) }, { default: () => '刪除' }),
          row.status === 'published'
            ? h(NButton, { size: 'small', onClick: () => handleSetDraft(row) }, { default: () => '設為草稿' })
            : h(NButton, { size: 'small', type: 'primary', onClick: () => handleSetPublish(row) }, { default: () => '發布' })
        ]
      });
    }
  }
];

function resetEditForm() {
  editForm.id = null;
  editForm.title = '';
  editForm.slug = '';
  editForm.description = '';
  editForm.content = '';
  editForm.category_id = null;
  editForm.status = 'draft';
  editForm.tags = [];
}

async function fetchArticles() {
  loading.value = true;
  try {
    const params: any = {
      page: pagination.currentPage,
      per_page: pagination.perPage,
      status: statusFilter.value || undefined,
      search: searchKeyword.value || undefined,
      category: categoryFilter.value || undefined,
      tags: tagFilter.value.length > 0 ? tagFilter.value.join(',') : undefined
    };
    const res = await getArticleList(params);
    articles.value = res.data || [];
    if (res.meta && res.meta.pagination) {
      pagination.currentPage = res.meta.pagination.current_page;
      pagination.totalPages = res.meta.pagination.total_pages;
      pagination.totalItems = res.meta.pagination.total_items;
      pagination.perPage = res.meta.pagination.per_page;
    }
  } catch (error) {
    message.error('取得文章列表失敗');
  } finally {
    loading.value = false;
  }
}

async function fetchCategoriesAndTags() {
  try {
    const [catRes, tagRes] = await Promise.all([
      getAllCategories(),
      getAllTags()
    ]);
    categoryOptions.value = (catRes.data || []).map((c: Category) => ({ label: c.name, value: c.slug! }));
    tagOptions.value = (tagRes.data || []).map((t: Tag) => ({ label: t.name, value: t.slug! }));
  } catch (error) {
    message.error('取得分類或標籤失敗');
  }
}

function openCreateModal() {
  resetEditForm();
  editMode.value = false;
  showEditModal.value = true;
}

async function openEditModal(row: any) {
  try {
    const res = await getArticleById(row.id);
    Object.assign(editForm, {
      id: res.data.id,
      title: res.data.title,
      slug: res.data.slug,
      description: res.data.description,
      content: res.data.content,
      category_id: res.data.category?.id || null,
      status: res.data.status,
      tags: (res.data.tags || []).map((t: Tag) => t.id)
    });
    editMode.value = true;
    showEditModal.value = true;
  } catch (error) {
    message.error('取得文章資料失敗');
  }
}

async function submitEditForm() {
  // 表單驗證
  editFormRef.value?.validate(async (errors: any) => {
    if (errors) return;
    try {
      if (editMode.value && editForm.id) {
        await updateArticle(editForm.id, {
          title: editForm.title,
          slug: editForm.slug,
          description: editForm.description,
          content: editForm.content,
          category_id: editForm.category_id,
          status: editForm.status,
          tags: editForm.tags
        });
        message.success('文章更新成功');
      } else {
        await createArticle({
          title: editForm.title,
          slug: editForm.slug,
          description: editForm.description,
          content: editForm.content,
          category_id: editForm.category_id,
          status: editForm.status,
          tags: editForm.tags
        });
        message.success('文章新增成功');
      }
      showEditModal.value = false;
      fetchArticles();
    } catch (error) {
      message.error('儲存失敗，請檢查欄位或稍後再試');
    }
  });
}

async function handleDelete(row: any) {
  if (!confirm('確定要刪除這篇文章嗎？')) return;
  try {
    await deleteArticle(row.id);
    message.success('刪除成功');
    fetchArticles();
  } catch (error) {
    message.error('刪除失敗');
  }
}

async function handleSetDraft(row: any) {
  try {
    await setArticleDraft(row.id);
    message.success('已設為草稿');
    fetchArticles();
  } catch (error) {
    message.error('狀態切換失敗');
  }
}

async function handleSetPublish(row: any) {
  try {
    await setArticlePublish(row.id);
    message.success('已發布');
    fetchArticles();
  } catch (error) {
    message.error('狀態切換失敗');
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
    icon: renderIcon(ChevronDownOutline)
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
  fetchArticles();
  fetchCategoriesAndTags();
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
.admin-article-container {
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
</style> 