<template>
  <div class="data-table-wrapper">
    <n-data-table
      ref="dataTableRef"
      :loading="loading"
      :columns="actionColumns"
      :data="data"
      :pagination="paginationConfig"
      :loading-text="TABLE_LOADING_TEXT"
      @update:page="onPageChange"
      @update:page-size="onPageSizeChange"
      @update:sorter="onSorterChange"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, h } from 'vue';
import { NDataTable, NButton, NSpace, PaginationProps } from 'naive-ui';
import { TABLE_LOADING_TEXT } from '../../constants';

// 列定義型別
interface TableColumn {
  title: string;
  key: string;
  sorter?: boolean;
  width?: number;
  render?: (row: any) => any;
}

// 分頁配置型別
interface TablePagination {
  currentPage: number;
  perPage: number;
  totalItems: number;
}

const props = defineProps({
  // 表格列配置
  columns: {
    type: Array as () => TableColumn[],
    required: true
  },
  // 數據
  data: {
    type: Array as () => any[],
    required: true
  },
  // 加載狀態
  loading: {
    type: Boolean,
    default: false
  },
  // 分頁配置
  pagination: {
    type: Object as () => TablePagination,
    required: true
  },
  // 是否顯示編輯按鈕
  showEdit: {
    type: Boolean,
    default: true
  },
  // 是否顯示刪除按鈕
  showDelete: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits<{
  (e: 'page-change', page: number): void;
  (e: 'page-size-change', pageSize: number): void;
  (e: 'sorter-change', sorter: { columnKey: string, order: 'ascend' | 'descend' | false } | null): void;
  (e: 'edit', row: any): void;
  (e: 'delete', row: any): void;
}>();

// 分頁配置
const paginationConfig = computed((): PaginationProps => ({
  page: props.pagination.currentPage,
  pageSize: props.pagination.perPage,
  showSizePicker: true,
  pageSizes: [10, 20, 50],
  onChange: (page: number) => emit('page-change', page),
  onUpdatePageSize: (pageSize: number) => emit('page-size-change', pageSize),
  itemCount: props.pagination.totalItems
}));

// 添加操作列
const actionColumns = computed(() => {
  if (!props.showEdit && !props.showDelete) {
    return props.columns;
  }

  const columns: TableColumn[] = [...props.columns];
  
  columns.push({
    title: '操作',
    key: 'actions',
    width: 150,
    render(row: any) {
      return h(
        NSpace,
        { justify: 'center', align: 'center' },
        {
          default: () => [
            // 編輯按鈕
            props.showEdit 
              ? h(
                  NButton,
                  {
                    size: 'small',
                    type: 'primary',
                    onClick: () => emit('edit', row)
                  },
                  { default: () => '編輯' }
                )
              : null,
            
            // 刪除按鈕
            props.showDelete
              ? h(
                  NButton,
                  {
                    size: 'small',
                    type: 'error',
                    onClick: () => emit('delete', row)
                  },
                  { default: () => '刪除' }
                )
              : null
          ]
        }
      );
    }
  });
  
  return columns;
});

// 頁碼變更
function onPageChange(page: number) {
  emit('page-change', page);
}

// 每頁數量變更
function onPageSizeChange(pageSize: number) {
  emit('page-size-change', pageSize);
}

// 排序變更
function onSorterChange(sorter: { columnKey: string, order: 'ascend' | 'descend' | false } | null) {
  emit('sorter-change', sorter);
}
</script>

<style scoped>
.data-table-wrapper {
  margin-top: 16px;
  width: 100%;
}

/* 響應式表格設計 */

/* 桌機：正常顯示 */
@media (min-width: 992px) {
  .data-table-wrapper {
    overflow-x: visible;
  }
}

/* 平板橫式：表格可橫向滾動 */
@media (min-width: 768px) and (max-width: 991px) {
  .data-table-wrapper {
    overflow-x: auto;
    width: 100%;
  }
  
  .data-table-wrapper :deep(.n-data-table) {
    min-width: var(--table-min-width);
  }
  
  .data-table-wrapper :deep(.n-data-table-wrapper) {
    border-radius: 8px;
  }
  
  .data-table-wrapper :deep(.n-data-table th) {
    white-space: nowrap;
    font-size: 0.875rem;
  }
  
  .data-table-wrapper :deep(.n-data-table td) {
    white-space: nowrap;
    font-size: 0.875rem;
  }
}

/* 平板直式：縮小字體和間距 */
@media (min-width: 576px) and (max-width: 767px) {
  .data-table-wrapper {
    overflow-x: auto;
    margin-top: 12px;
  }
  
  .data-table-wrapper :deep(.n-data-table) {
    min-width: var(--table-min-width);
    font-size: 0.8rem;
  }
  
  .data-table-wrapper :deep(.n-data-table th) {
    padding: 8px 12px;
    font-size: 0.75rem;
    white-space: nowrap;
  }
  
  .data-table-wrapper :deep(.n-data-table td) {
    padding: 8px 12px;
    font-size: 0.8rem;
    white-space: nowrap;
  }
  
  .data-table-wrapper :deep(.n-pagination) {
    justify-content: center;
    margin-top: 16px;
  }
  
  .data-table-wrapper :deep(.n-pagination .n-pagination-item) {
    min-width: 32px;
    height: 32px;
  }
}

/* 手機：極簡表格 */
@media (max-width: 575px) {
  .data-table-wrapper {
    margin-top: 8px;
    overflow-x: auto;
  }
  
  .data-table-wrapper :deep(.n-data-table) {
    min-width: var(--table-mobile-min-width);
    font-size: 0.75rem;
  }
  
  .data-table-wrapper :deep(.n-data-table th) {
    padding: 6px 8px;
    font-size: 0.7rem;
    white-space: nowrap;
  }
  
  .data-table-wrapper :deep(.n-data-table td) {
    padding: 6px 8px;
    font-size: 0.75rem;
    white-space: nowrap;
  }
  
  /* 行動按鈕優化 */
  .data-table-wrapper :deep(.n-button) {
    font-size: 0.7rem;
    padding: 2px 6px;
    min-width: 48px;
    height: 28px;
  }
  
  /* 分頁器移動端優化 */
  .data-table-wrapper :deep(.n-pagination) {
    justify-content: center;
    margin-top: 12px;
    flex-wrap: wrap;
  }
  
  .data-table-wrapper :deep(.n-pagination .n-pagination-item) {
    min-width: 28px;
    height: 28px;
    margin: 2px;
    font-size: 0.75rem;
  }
  
  .data-table-wrapper :deep(.n-pagination .n-pagination-prefix),
  .data-table-wrapper :deep(.n-pagination .n-pagination-suffix) {
    font-size: 0.7rem;
  }
}

/* 極小螢幕：進一步優化 */
@media (max-width: 479px) {
  .data-table-wrapper {
    margin-top: 4px;
  }
  
  .data-table-wrapper :deep(.n-data-table th) {
    padding: 4px 6px;
    font-size: 0.65rem;
  }
  
  .data-table-wrapper :deep(.n-data-table td) {
    padding: 4px 6px;
    font-size: 0.7rem;
  }
  
  .data-table-wrapper :deep(.n-button) {
    font-size: 0.65rem;
    padding: 2px 4px;
    min-width: 40px;
    height: 24px;
  }
  
  .data-table-wrapper :deep(.n-pagination .n-pagination-item) {
    min-width: 24px;
    height: 24px;
    font-size: 0.7rem;
  }
}
</style> 