import { ref, reactive, computed } from 'vue';
import { articleApi, categoryApi, tagApi } from '../api';
import { ERROR_MESSAGES, STATUS_OPTIONS } from '../constants';
import type { Article, ArticleListParams, CreateArticleParams } from '../types/article';
import type { Category } from '../types/category';
import type { Tag } from '../types/tag';
import type { SelectOption } from 'naive-ui';

/**
 * 文章列表管理邏輯
 */
export function useArticles(message: any) {
  // 文章列表狀態
  const articles = ref<Article[]>([]);
  const loading = ref(false);
  const params = ref<ArticleListParams>({
    page: 1,
    per_page: 10,
    search: '',
    status: 'all',
    category: undefined,
    tags: []
  });
  
  // 分頁
  const pagination = ref({
    currentPage: 1,
    perPage: 10,
    totalItems: 0
  });
  
  // 篩選狀態
  const filterVisible = ref(false);
  const statusOptions = ref(STATUS_OPTIONS);
  const categoryFilter = ref<string | undefined>(undefined);
  const tagFilters = ref<string[]>([]);
  const categoryOptions = ref<SelectOption[]>([]);
  const tagOptions = ref<SelectOption[]>([]);
  
  // 加載篩選選項
  async function loadFilterOptions() {
    try {
      const [categoriesResponse, tagsResponse] = await Promise.all([
        categoryApi.getList(),
        tagApi.getList()
      ]);
      
      // 處理分類選項
      categoryOptions.value = [
        { label: '全部', value: 'all' },
        ...categoriesResponse.data.map((category: Category) => ({
          label: category.name,
          value: category.slug
        }))
      ];
      
      // 處理標籤選項
      tagOptions.value = tagsResponse.data.map((tag: Tag) => ({
        label: tag.name,
        value: tag.slug
      }));
    } catch (error) {
      message.error('獲取篩選選項失敗');
      console.error(error);
    }
  }
  
  // 獲取文章列表
  async function fetchArticles() {
    loading.value = true;
    
    try {
      // 構建 API 參數
      const apiParams = { ...params.value };
      
      // 處理分類篩選
      if (categoryFilter.value && categoryFilter.value !== 'all') {
        apiParams.category = categoryFilter.value;
      } else {
        delete apiParams.category;
      }
      
      // 處理標籤篩選
      if (tagFilters.value && tagFilters.value.length > 0) {
        apiParams.tags = tagFilters.value;
      } else {
        delete apiParams.tags;
      }
      
      const response = await articleApi.getList(apiParams);
      articles.value = response.data;
      
      if (response.meta?.pagination) {
        pagination.value = {
          currentPage: response.meta.pagination.current_page,
          perPage: response.meta.pagination.per_page,
          totalItems: response.meta.pagination.total_items
        };
      }
    } catch (error) {
      message.error(ERROR_MESSAGES.FETCH_FAILED);
      console.error(error);
    } finally {
      loading.value = false;
    }
  }
  
  // 搜尋處理
  function handleSearch(value: string) {
    params.value.search = value;
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
    params.value.search = '';
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
    // 狀態
    articles,
    loading,
    params,
    pagination,
    filterVisible,
    
    // 選項
    statusOptions,
    categoryOptions,
    tagOptions,
    categoryFilter,
    tagFilters,
    
    // 方法
    fetchArticles,
    loadFilterOptions,
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

/**
 * 文章表單管理邏輯
 */
export function useArticleForm(message: any, onSuccess: () => void) {
  // 表單狀態
  const DEFAULT_FORM = {
    title: '',
    slug: '',
    description: '',
    content: '',
    category_id: null,
    status: 'draft' as const,
    tags: []
  };
  
  const formModel = reactive<CreateArticleParams>({ ...DEFAULT_FORM });
  const showForm = ref(false);
  const isEdit = ref(false);
  const editingId = ref<number | null>(null);
  
  // 計算屬性
  const formTitle = computed(() => isEdit.value ? '編輯文章' : '新增文章');
  
  // 打開新增表單
  function openCreateForm() {
    Object.assign(formModel, DEFAULT_FORM);
    isEdit.value = false;
    editingId.value = null;
    showForm.value = true;
  }
  
  // 打開編輯表單
  function openEditForm(row: Article) {
    isEdit.value = true;
    editingId.value = row.id || null;
    
    // 填充表單數據
    Object.assign(formModel, {
      title: row.title,
      slug: row.slug,
      description: row.description || '',
      content: row.content,
      category_id: row.category?.id || null,
      status: row.status,
      tags: row.tags?.map(tag => tag.id) || []
    });
    
    showForm.value = true;
  }
  
  // 表單提交
  async function handleFormSubmit(data: CreateArticleParams) {
    try {
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
      
      showForm.value = false;
      onSuccess();
    } catch (error) {
      message.error(isEdit.value ? ERROR_MESSAGES.UPDATE_FAILED : ERROR_MESSAGES.CREATE_FAILED);
      console.error(error);
    }
  }
  
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
 * 文章刪除邏輯
 */
export function useArticleDelete(message: any, onSuccess: () => void) {
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
  
  // 刪除文章
  async function handleDelete() {
    if (!deletingId.value) return;
    
    try {
      await articleApi.admin.delete(deletingId.value);
      message.success('文章刪除成功');
      onSuccess();
    } catch (error) {
      message.error(ERROR_MESSAGES.DELETE_FAILED);
      console.error(error);
    } finally {
      deletingId.value = null;
      showDeleteConfirm.value = false;
    }
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
 * 選項數據獲取邏輯
 */
export function useOptions(message: any) {
  const categoryOptions = ref<SelectOption[]>([]);
  const tagOptions = ref<SelectOption[]>([]);
  
  // 加載選項數據
  async function loadOptions() {
    try {
      const [categoriesResponse, tagsResponse] = await Promise.all([
        categoryApi.getList(),
        tagApi.getList()
      ]);
      
      categoryOptions.value = categoriesResponse.data.map((category: Category) => ({
        label: category.name,
        value: category.id
      }));
      
      tagOptions.value = tagsResponse.data.map((tag: Tag) => ({
        label: tag.name,
        value: tag.id
      }));
    } catch (error) {
      message.error('獲取選項失敗');
      console.error(error);
    }
  }
  
  return {
    categoryOptions,
    tagOptions,
    loadOptions
  };
} 