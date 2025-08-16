import { ref, reactive, computed } from 'vue';
import { categoryApi } from '../api';
import { useStaticDataStore } from '../stores/staticData';
import { usePagination } from './usePagination';
import type { Category, CreateCategoryParams } from '../types/category';

/**
 * 分類列表管理邏輯
 */
export function useCategories(message: any) {
  const staticDataStore = useStaticDataStore();
  
  // 搜尋與排序狀態
  const searchText = ref('');
  const sortBy = ref<'name' | 'created_at'>('created_at');
  const sortDirection = ref<'asc' | 'desc'>('desc');
  
  // 分頁邏輯
  const {
    handlePageChange,
    handlePageSizeChange,
    resetToFirstPage,
    paginateData,
    createPaginationInfo
  } = usePagination(10);
  
  // 篩選後的分類
  const filteredCategories = computed(() => {
    let result = staticDataStore.categories;
    
    // 搜尋篩選
    if (searchText.value) {
      const search = searchText.value.toLowerCase();
      result = result.filter(cat => 
        cat.name.toLowerCase().includes(search) ||
        cat.slug.toLowerCase().includes(search)
      );
    }
    
    // 排序
    result = [...result].sort((a, b) => {
      const aValue = a[sortBy.value];
      const bValue = b[sortBy.value];
      const direction = sortDirection.value === 'asc' ? 1 : -1;
      return aValue > bValue ? direction : -direction;
    });
    
    return result;
  });
  
  // 分頁後的結果
  const categories = computed(() => paginateData(filteredCategories.value));
  
  // 分頁資訊
  const pagination = computed(() => createPaginationInfo(filteredCategories.value.length));
  
  // 載入狀態
  const loading = computed(() => staticDataStore.loading.categories);
  
  // 初始化載入
  async function fetchCategories() {
    await staticDataStore.ensureLoaded();
  }
  
  // 搜尋
  function handleSearch(value: string) {
    searchText.value = value;
    resetToFirstPage();
  }
  
  // 排序
  function handleSorterChange(sorter: { columnKey: string, order: 'ascend' | 'descend' | false } | null) {
    if (sorter && sorter.order) {
      sortBy.value = sorter.columnKey as 'name' | 'created_at';
      sortDirection.value = sorter.order === 'ascend' ? 'asc' : 'desc';
    } else {
      sortBy.value = 'created_at';
      sortDirection.value = 'desc';
    }
    resetToFirstPage();
  }
  
  return {
    categories,
    loading,
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
  const staticDataStore = useStaticDataStore();
  
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
  
  // 表單提交（樂觀更新策略）
  async function handleFormSubmit(data: CreateCategoryParams): Promise<void> {
    const isEditMode = isEdit.value && editingId.value;
    let result;
    
    if (isEditMode) {
      // 更新操作
      result = await categoryApi.update({
        id: editingId.value!,
        data
      });
      
      // API 成功後更新 store
      if (result?.data) {
        const updated = staticDataStore.updateCategory(result.data);
        if (!updated) {
          console.warn('[useCategoryForm] Category update in store failed, forcing reload');
          await staticDataStore.ensureLoaded(true);
        }
      }
      
      message.success('分類更新成功');
    } else {
      // 創建操作
      result = await categoryApi.create(data);
      
      // API 成功後新增到 store
      if (result?.data) {
        staticDataStore.addCategory(result.data);
      }
      
      message.success('分類創建成功');
    }
    
    // 操作成功後關閉表單
    showForm.value = false;
    onSuccess();
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
  const staticDataStore = useStaticDataStore();
  
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
  
  // 刪除分類（樂觀更新策略）
  async function handleDelete(): Promise<void> {
    if (!deletingId.value) {
      console.warn('[useCategoryDelete] No category ID to delete');
      return;
    }
    
    const categoryId = deletingId.value;
    const categoryName = deletingCategory.value?.name || `ID: ${categoryId}`;
    
    await categoryApi.delete(categoryId);
    
    // API 成功後從 store 移除
    const removed = staticDataStore.removeCategory(categoryId);
    if (!removed) {
      console.warn(`[useCategoryDelete] Category ${categoryId} not found in store, forcing reload`);
      await staticDataStore.ensureLoaded(true);
    }
    
    message.success(`分類「${categoryName}」刪除成功`);
    onSuccess();
    
    // 清理狀態
    deletingId.value = null;
    deletingCategory.value = null;
    showDeleteConfirm.value = false;
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