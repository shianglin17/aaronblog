import { ref, reactive, computed } from 'vue';
import { tagApi } from '../api';
import { ERROR_MESSAGES } from '../constants';
import { useStaticDataStore } from '../stores/staticData';
import { usePagination } from './usePagination';
import type { Tag, CreateTagParams } from '../types/tag';

/**
 * 標籤列表管理邏輯
 */
export function useTags(message: any) {
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
  
  // 篩選後的標籤
  const filteredTags = computed(() => {
    let result = staticDataStore.tags;
    
    // 搜尋篩選
    if (searchText.value) {
      const search = searchText.value.toLowerCase();
      result = result.filter(tag => 
        tag.name.toLowerCase().includes(search) ||
        tag.slug.toLowerCase().includes(search)
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
  const tags = computed(() => paginateData(filteredTags.value));
  
  // 分頁資訊
  const pagination = computed(() => createPaginationInfo(filteredTags.value.length));
  
  // 載入狀態
  const loading = computed(() => staticDataStore.loading.tags);
  
  // 初始化載入
  async function fetchTags() {
    try {
      await staticDataStore.ensureLoaded();
    } catch (error) {
      message.error(ERROR_MESSAGES.FETCH_FAILED);
      console.error(error);
    }
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
    tags,
    loading,
    pagination,
    fetchTags,
    handleSearch,
    handlePageChange,
    handlePageSizeChange,
    handleSorterChange
  };
}

/**
 * 標籤表單管理邏輯
 */
export function useTagForm(message: any, onSuccess: () => void) {
  const staticDataStore = useStaticDataStore();
  
  // 表單狀態
  const DEFAULT_FORM = {
    name: '',
    slug: ''
  };
  
  const formModel = reactive<CreateTagParams>({ ...DEFAULT_FORM });
  const showForm = ref(false);
  const isEdit = ref(false);
  const editingId = ref<number | null>(null);
  
  // 計算屬性
  const formTitle = computed(() => isEdit.value ? '編輯標籤' : '新增標籤');
  
  // 打開新增表單
  function openCreateForm() {
    Object.assign(formModel, DEFAULT_FORM);
    isEdit.value = false;
    editingId.value = null;
    showForm.value = true;
  }
  
  // 打開編輯表單
  function openEditForm(row: Tag) {
    isEdit.value = true;
    editingId.value = row.id || null;
    
    // 填充表單數據
    Object.assign(formModel, {
      name: row.name,
      slug: row.slug
    });
    
    showForm.value = true;
  }
  
  // 表單提交（樂觀更新策略）
  async function handleFormSubmit(data: CreateTagParams): Promise<void> {
    const isEditMode = isEdit.value && editingId.value;
    
    try {
      let result;
      
      if (isEditMode) {
        result = await tagApi.update({
          id: editingId.value!,
          data
        });
        
        if (result?.data) {
          const updated = staticDataStore.updateTag(result.data);
          if (!updated) {
            console.warn('[useTagForm] Tag update in store failed, forcing reload');
            await staticDataStore.ensureLoaded(true);
          }
        }
        
        message.success('標籤更新成功');
      } else {
        result = await tagApi.create(data);
        
        if (result?.data) {
          staticDataStore.addTag(result.data);
        }
        
        message.success('標籤創建成功');
      }
      
      showForm.value = false;
      onSuccess();
      
    } catch (error) {
      const errorMsg = isEditMode ? ERROR_MESSAGES.UPDATE_FAILED : ERROR_MESSAGES.CREATE_FAILED;
      message.error(errorMsg);
      console.error(`[useTagForm] ${isEditMode ? 'Update' : 'Create'} failed:`, error);
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
 * 標籤刪除邏輯
 */
export function useTagDelete(message: any, onSuccess: () => void) {
  const staticDataStore = useStaticDataStore();
  
  const showDeleteConfirm = ref(false);
  const deletingId = ref<number | null>(null);
  const deletingTag = ref<Tag | null>(null);
  
  // 打開刪除確認
  function openDeleteConfirm(row: Tag) {
    deletingId.value = row.id || null;
    deletingTag.value = row;
    showDeleteConfirm.value = true;
  }
  
  // 取消刪除
  function cancelDelete() {
    deletingId.value = null;
    deletingTag.value = null;
    showDeleteConfirm.value = false;
  }
  
  // 刪除標籤（樂觀更新策略）
  async function handleDelete(): Promise<void> {
    if (!deletingId.value) {
      console.warn('[useTagDelete] No tag ID to delete');
      return;
    }
    
    const tagId = deletingId.value;
    const tagName = deletingTag.value?.name || `ID: ${tagId}`;
    
    try {
      await tagApi.delete(tagId);
      
      const removed = staticDataStore.removeTag(tagId);
      if (!removed) {
        console.warn(`[useTagDelete] Tag ${tagId} not found in store, forcing reload`);
        await staticDataStore.ensureLoaded(true);
      }
      
      message.success(`標籤「${tagName}」刪除成功`);
      onSuccess();
      
    } catch (error) {
      message.error(ERROR_MESSAGES.DELETE_FAILED);
      console.error(`[useTagDelete] Failed to delete tag ${tagId}:`, error);
    } finally {
      deletingId.value = null;
      deletingTag.value = null;
      showDeleteConfirm.value = false;
    }
  }
  
  return {
    showDeleteConfirm,
    deletingId,
    deletingTag,
    openDeleteConfirm,
    cancelDelete,
    handleDelete
  };
} 