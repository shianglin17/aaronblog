import { ref, reactive, computed } from 'vue';
import { categoryApi } from '../api';
import { ERROR_MESSAGES } from '../constants';
import type { Category, CreateCategoryParams } from '../types/category';

/**
 * 分類列表管理邏輯
 */
export function useCategories(message: any) {
  // 分類列表狀態
  const categories = ref<Category[]>([]);
  const loading = ref(false);
  const params = ref({
    page: 1,
    per_page: 10,
    search: '',
    sort_by: 'created_at' as 'created_at' | 'updated_at' | 'name',
    sort_direction: 'desc' as 'asc' | 'desc'
  });
  
  // 分頁
  const pagination = ref({
    currentPage: 1,
    perPage: 10,
    totalItems: 0
  });
  
  // 獲取分類列表
  async function fetchCategories() {
    loading.value = true;
    
    try {
      const response = await categoryApi.getList();
      categories.value = response.data;
      
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
    fetchCategories();
  }
  
  // 分頁處理
  function handlePageChange(page: number) {
    params.value.page = page;
    fetchCategories();
  }
  
  // 每頁筆數變更
  function handlePageSizeChange(pageSize: number) {
    params.value.per_page = pageSize;
    params.value.page = 1;
    fetchCategories();
  }
  
  // 排序處理
  function handleSorterChange(sorter: { columnKey: string, order: 'ascend' | 'descend' | false } | null) {
    if (sorter) {
      params.value.sort_by = sorter.columnKey as 'created_at' | 'updated_at' | 'name';
      params.value.sort_direction = sorter.order === 'ascend' ? 'asc' : 'desc';
    } else {
      params.value.sort_by = 'created_at';
      params.value.sort_direction = 'desc';
    }
    fetchCategories();
  }
  
  return {
    categories,
    loading,
    params,
    pagination,
    fetchCategories,
    handleSearch,
    handlePageChange,
    handlePageSizeChange,
    handleSorterChange
  };
}

/**
 * 分類表單管理邏輯
 */
export function useCategoryForm(message: any, onSuccess: () => void) {
  // 表單狀態
  const DEFAULT_FORM = {
    name: '',
    slug: '',
    description: ''
  };
  
  const formModel = reactive<CreateCategoryParams>({ ...DEFAULT_FORM });
  const showForm = ref(false);
  const isEdit = ref(false);
  const editingId = ref<number | null>(null);
  
  // 計算屬性
  const formTitle = computed(() => isEdit.value ? '編輯分類' : '新增分類');
  
  // 打開新增表單
  function openCreateForm() {
    Object.assign(formModel, DEFAULT_FORM);
    isEdit.value = false;
    editingId.value = null;
    showForm.value = true;
  }
  
  // 打開編輯表單
  function openEditForm(row: Category) {
    isEdit.value = true;
    editingId.value = row.id || null;
    
    // 填充表單數據
    Object.assign(formModel, {
      name: row.name,
      slug: row.slug,
      description: row.description || ''
    });
    
    showForm.value = true;
  }
  
  // 表單提交
  async function handleFormSubmit(data: CreateCategoryParams) {
    try {
      if (isEdit.value && editingId.value) {
        await categoryApi.update({
          id: editingId.value,
          data
        });
        message.success('分類更新成功');
      } else {
        await categoryApi.create(data);
        message.success('分類創建成功');
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
 * 分類刪除邏輯
 */
export function useCategoryDelete(message: any, onSuccess: () => void) {
  const showDeleteConfirm = ref(false);
  const deletingId = ref<number | null>(null);
  const deletingCategory = ref<Category | null>(null);
  
  // 打開刪除確認
  function openDeleteConfirm(row: Category) {
    deletingId.value = row.id || null;
    deletingCategory.value = row;
    showDeleteConfirm.value = true;
  }
  
  // 取消刪除
  function cancelDelete() {
    deletingId.value = null;
    deletingCategory.value = null;
    showDeleteConfirm.value = false;
  }
  
  // 刪除分類
  async function handleDelete() {
    if (!deletingId.value) return;
    
    try {
      await categoryApi.delete(deletingId.value);
      message.success('分類刪除成功');
      onSuccess();
    } catch (error) {
      message.error(ERROR_MESSAGES.DELETE_FAILED);
      console.error(error);
    } finally {
      deletingId.value = null;
      deletingCategory.value = null;
      showDeleteConfirm.value = false;
    }
  }
  
  return {
    showDeleteConfirm,
    deletingId,
    deletingCategory,
    openDeleteConfirm,
    cancelDelete,
    handleDelete
  };
} 