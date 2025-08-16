import { ref, reactive, computed, readonly, watch } from 'vue';
import { articleApi } from '../api';
import { ERROR_MESSAGES, STATUS_OPTIONS, DEFAULT_ARTICLE_FORM } from '../constants';
import { useStaticDataStore } from '../stores/staticData';
import type { Article, ArticleListParams, CreateArticleParams } from '../types/article';

// 型別定義
type FilterOption = {
  label: string;
  value: string | number;
};

/**
 * 管理後台文章列表管理邏輯
 * 
 * 提供管理後台文章列表的篩選、分頁、搜尋等功能
 * 使用統一的 staticDataStore 管理分類與標籤資料
 * 專用於管理後台，使用 admin API
 */
export function useAdminArticles(message: any) {
  const staticDataStore = useStaticDataStore();
  
  // 核心狀態
  const articles = ref<Article[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  
  // API 參數
  const params = ref<ArticleListParams>({
    page: 1,
    per_page: 10,
    status: 'all'
  });
  
  // 分頁狀態
  const pagination = ref({
    currentPage: 1,
    perPage: 10,
    totalItems: 0
  });
  
  // 篩選狀態
  const filterVisible = ref(false);
  const statusOptions = ref(STATUS_OPTIONS);
  const searchQuery = ref(''); // 用於 UI 顯示的搜尋字串
  const categoryFilter = ref<string | undefined>(undefined);
  const tagFilters = ref<string[]>([]);
  
  // 從 store 獲取篩選選項（響應式）
  const categoryOptions = computed<FilterOption[]>(() => [
    { label: '全部', value: 'all' },
    ...staticDataStore.categories.map(category => ({
      label: `${category.name}${category.articles_count ? ` (${category.articles_count})` : ''}`,
      value: category.slug
    }))
  ]);
  
  const tagOptions = computed<FilterOption[]>(() => 
    staticDataStore.tags.map(tag => ({
      label: `${tag.name}${tag.articles_count ? ` (${tag.articles_count})` : ''}`,
      value: tag.slug
    }))
  );
  
  /**
   * 確保篩選選項已載入
   * 使用 store 的統一載入機制
   */
  async function loadFilterOptions(): Promise<void> {
    await staticDataStore.ensureLoaded();
    error.value = null;
  }
  
  // 獲取文章列表 - 使用管理後台 API
  async function fetchArticles() {
    loading.value = true;
    
    // 構建 API 參數 - 只包含有值的參數
    const apiParams: ArticleListParams = { ...params.value };
    
    // 處理搜尋參數 - 只有非空時才加入
    if (params.value.search && params.value.search.trim()) {
      apiParams.search = params.value.search.trim();
    }
    
    // 處理分類篩選
    if (categoryFilter.value && categoryFilter.value !== 'all') {
      apiParams.category = categoryFilter.value;
    }
    
    // 處理標籤篩選
    if (tagFilters.value && tagFilters.value.length > 0) {
      apiParams.tags = tagFilters.value;
    }
    
    // 使用管理後台專用 API
    const response = await articleApi.admin.getList(apiParams);
    articles.value = response.data;
    
    if (response.meta?.pagination) {
      pagination.value = {
        currentPage: response.meta.pagination.current_page,
        perPage: response.meta.pagination.per_page,
        totalItems: response.meta.pagination.total_items
      };
    }
    
    loading.value = false;
  }
  
  // 搜尋處理
  function handleSearch(value: string) {
    searchQuery.value = value; // 更新 UI 顯示
    
    // 只有當有實際搜尋內容時才設定，否則移除搜尋參數
    if (value && value.trim()) {
      params.value.search = value.trim();
    } else {
      delete params.value.search;
    }
    params.value.page = 1;
    fetchArticles();
  }
  
  // 狀態變更處理
  function handleStatusChange(value: ArticleListParams['status']) {
    params.value.status = value;
    params.value.page = 1;
    fetchArticles();
  }
  
  // 分類篩選處理
  function handleCategoryChange(value: string) {
    categoryFilter.value = value === 'all' ? undefined : value;
    params.value.page = 1;
    fetchArticles();
  }
  
  // 標籤篩選處理
  function handleTagsChange(values: string[]) {
    tagFilters.value = values;
    params.value.page = 1;
    fetchArticles();
  }
  
  // 分頁處理
  function handlePageChange(page: number) {
    params.value.page = page;
    fetchArticles();
  }
  
  // 每頁筆數變更
  function handlePageSizeChange(pageSize: number) {
    params.value.per_page = pageSize;
    params.value.page = 1;
    fetchArticles();
  }
  
  // 排序處理
  function handleSorterChange(sorter: { columnKey: string, order: 'ascend' | 'descend' | false } | null) {
    if (sorter) {
      params.value.sort_by = sorter.columnKey as 'created_at' | 'updated_at' | 'title';
      params.value.sort_direction = sorter.order === 'ascend' ? 'asc' : 'desc';
    } else {
      params.value.sort_by = 'created_at';
      params.value.sort_direction = 'desc';
    }
    fetchArticles();
  }
  
  // 重置所有篩選
  function resetFilters() {
    // 重置 UI 狀態
    searchQuery.value = '';
    // 移除搜尋參數而不是設為空字串
    delete params.value.search;
    params.value.status = 'all';
    categoryFilter.value = undefined;
    tagFilters.value = [];
    params.value.page = 1;
    fetchArticles();
  }
  
  // 開關篩選面板
  function toggleFilterPanel() {
    filterVisible.value = !filterVisible.value;
  }
  
  return {
    // 核心狀態
    articles,
    loading,
    error: readonly(error),
    params,
    pagination,
    filterVisible,
    
    // 篩選選項
    statusOptions,
    categoryOptions,
    tagOptions,
    searchQuery,
    categoryFilter,
    tagFilters,
    
    // 核心功能
    fetchArticles,
    loadFilterOptions,
    
    // 事件處理
    handleSearch,
    handleStatusChange,
    handleCategoryChange,
    handleTagsChange,
    handlePageChange,
    handlePageSizeChange,
    handleSorterChange,
    resetFilters,
    toggleFilterPanel
  };
}

// 使用統一的表單初始狀態
const INITIAL_ARTICLE_FORM_STATE = {
  ...DEFAULT_ARTICLE_FORM,
  tags: [] as number[] // 明確標籤為數字陣列類型
};

/**
 * 管理後台文章表單管理邏輯
 * 專用於管理後台，使用 admin API
 */
export function useAdminArticleForm(message: any, onSuccess: () => void) {
  // 使用初始狀態常量來初始化 formModel，確保是深拷貝
  const formModel = reactive<CreateArticleParams>({ 
    ...INITIAL_ARTICLE_FORM_STATE,
    tags: [...INITIAL_ARTICLE_FORM_STATE.tags] // 確保 tags 也是新的陣列實例
  });

  const showForm = ref(false);
  const isEdit = ref(false);
  const editingId = ref<number | null>(null);

  const formTitle = computed(() => isEdit.value ? '編輯文章' : '新增文章');

  /**
   * 重置表單模型到初始狀態，並清除編輯上下文。
   */
  function resetAndInitializeFormModel() {
    formModel.title = INITIAL_ARTICLE_FORM_STATE.title;
    formModel.slug = INITIAL_ARTICLE_FORM_STATE.slug;
    formModel.description = INITIAL_ARTICLE_FORM_STATE.description;
    formModel.content = INITIAL_ARTICLE_FORM_STATE.content;
    formModel.category_id = INITIAL_ARTICLE_FORM_STATE.category_id;
    formModel.status = INITIAL_ARTICLE_FORM_STATE.status;
    formModel.tags = [...INITIAL_ARTICLE_FORM_STATE.tags]; // 關鍵：賦予新的陣列實例

    editingId.value = null; // 重置正在編輯的ID
    // isEdit 的狀態將由 openCreateForm 或 openEditForm 明確設定
  }

  // 打開新增表單
  function openCreateForm() {
    resetAndInitializeFormModel(); // 先徹底重置表單
    isEdit.value = false;
    showForm.value = true;
  }

  // 打開編輯表單
  function openEditForm(row: Article) {
    resetAndInitializeFormModel(); // 先徹底重置表單，清除任何殘留狀態
    isEdit.value = true;
    editingId.value = row.id || null;
    
    // 從 'row' 物件填充表單數據
    formModel.title = row.title;
    formModel.slug = row.slug || ''; // 提供預設空字串以防 slug 為 null/undefined
    formModel.description = row.description || '';
    formModel.content = row.content;
    formModel.category_id = row.category?.id || null;
    formModel.status = row.status;
    // 確保賦予新的陣列實例
    formModel.tags = row.tags?.map(tag => tag.id) || []; 
    
    showForm.value = true;
  }

  // 表單提交 - 使用管理後台 API
  async function handleFormSubmit(data: CreateArticleParams) {
    if (isEdit.value && editingId.value) {
      await articleApi.admin.update({
        id: editingId.value,
        data
      });
      message.success('文章更新成功');
    } else {
      await articleApi.admin.create(data);
      message.success('文章創建成功');
    }
    
    showForm.value = false; // 關閉模態框，這會觸發下面的 watch
    onSuccess(); // 調用成功回調
  }

  // 監聽 showForm 狀態變化
  // 當模態框從顯示變為隱藏時，重置表單模型
  // 這涵蓋了點擊取消按鈕、點擊模態框外部或提交成功後關閉模態框的情況
  watch(showForm, (isVisible) => {
    if (!isVisible) {
      // 使用 setTimeout 將重置操作推遲到下一個事件循環
      // 這有助於確保在表單完全關閉後執行重置，避免可能的衝突
      setTimeout(() => {
        resetAndInitializeFormModel();
        isEdit.value = false; // 同時重置 isEdit 狀態
      }, 0);
    }
  });
  
  return {
    formModel,
    showForm,
    isEdit,
    editingId,
    formTitle,
    openCreateForm,
    openEditForm,
    handleFormSubmit
  };
}

/**
 * 管理後台文章刪除邏輯
 * 專用於管理後台，使用 admin API
 */
export function useAdminArticleDelete(message: any, onSuccess: () => void) {
  const showDeleteConfirm = ref(false);
  const deletingId = ref<number | null>(null);
  
  // 打開刪除確認
  function openDeleteConfirm(row: Article) {
    deletingId.value = row.id || null;
    showDeleteConfirm.value = true;
  }
  
  // 取消刪除
  function cancelDelete() {
    deletingId.value = null;
    showDeleteConfirm.value = false;
  }
  
  // 刪除文章 - 使用管理後台 API
  async function handleDelete() {
    if (!deletingId.value) return;
    
    await articleApi.admin.delete(deletingId.value);
    message.success('文章刪除成功');
    onSuccess();
    
    deletingId.value = null;
    showDeleteConfirm.value = false;
  }
  
  return {
    showDeleteConfirm,
    deletingId,
    openDeleteConfirm,
    cancelDelete,
    handleDelete
  };
}

/**
 * 管理後台表單選項數據管理
 * 
 * 提供文章編輯表單使用的分類與標籤選項
 * 使用 ID 作為 value 以便於表單提交
 */
export function useAdminOptions(message: any) {
  const staticDataStore = useStaticDataStore();
  const error = ref<string | null>(null);
  
  // 表單用選項（使用 ID）
  const categoryOptions = computed<FilterOption[]>(() => 
    staticDataStore.categories.map(category => ({
      label: category.name,
      value: category.id
    }))
  );
  
  const tagOptions = computed<FilterOption[]>(() => 
    staticDataStore.tags.map(tag => ({
      label: tag.name,
      value: tag.id
    }))
  );
  
  // 載入狀態（從 store 獲取）
  const loading = computed(() => staticDataStore.loading.global);
  
  /**
   * 載入選項數據
   * 使用 store 的統一管理機制
   */
  async function loadOptions(): Promise<void> {
    await staticDataStore.ensureLoaded();
    error.value = null;
  }
  
  return {
    // Data
    categoryOptions,
    tagOptions,
    
    // State
    loading,
    error: readonly(error),
    
    // Actions
    loadOptions
  };
}