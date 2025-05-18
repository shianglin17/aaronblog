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
}
</style> 