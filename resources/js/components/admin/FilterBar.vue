<template>
  <div class="filter-bar">
    <n-space>
      <slot name="prefix"></slot>
      
      <!-- 搜索框 -->
      <n-input
        v-if="showSearch"
        v-model:value="searchKeyword"
        placeholder="搜尋..."
        clearable
        @keyup.enter="handleSearch"
      >
        <template #suffix>
          <n-button quaternary circle @click="handleSearch">
            <template #icon>
              <n-icon><search-outline /></n-icon>
            </template>
          </n-button>
        </template>
      </n-input>
      
      <!-- 狀態篩選 -->
      <n-select
        v-if="showStatus"
        v-model:value="statusFilter"
        :options="STATUS_OPTIONS"
        placeholder="狀態"
        clearable
        class="status-filter"
        @update:value="handleStatusChange"
      />
      
      <slot></slot>
      
      <slot name="suffix"></slot>
    </n-space>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { SearchOutline } from '@vicons/ionicons5';
import { STATUS_OPTIONS } from '../../constants';

// 定義屬性
const props = defineProps({
  // 是否顯示搜索框
  showSearch: {
    type: Boolean,
    default: true
  },
  // 是否顯示狀態篩選
  showStatus: {
    type: Boolean,
    default: false
  },
  // 搜索關鍵字
  search: {
    type: String,
    default: ''
  },
  // 狀態篩選值
  status: {
    type: String,
    default: 'all'
  }
});

// 定義事件
const emit = defineEmits<{
  (e: 'search', value: string): void;
  (e: 'status-change', value: string): void;
  (e: 'update:search', value: string): void;
  (e: 'update:status', value: string): void;
}>();

// 狀態
const searchKeyword = ref(props.search);
const statusFilter = ref(props.status);

// 監聽外部搜索關鍵字變化
watch(() => props.search, (newVal) => {
  searchKeyword.value = newVal;
});

// 監聽外部狀態變化
watch(() => props.status, (newVal) => {
  statusFilter.value = newVal;
});

// 處理搜索
function handleSearch() {
  emit('update:search', searchKeyword.value);
  emit('search', searchKeyword.value);
}

// 處理狀態變化
function handleStatusChange(value: string) {
  emit('update:status', value);
  emit('status-change', value);
}
</script>

<style scoped>
.filter-bar {
  margin-bottom: 16px;
}

.status-filter {
  width: 120px;
}

/* 響應式篩選器設計 */

/* 桌機：正常顯示 */
@media (min-width: 992px) {
  .filter-bar :deep(.n-space) {
    flex-wrap: nowrap;
  }
}

/* 平板：適度調整 */
@media (min-width: 768px) and (max-width: 991px) {
  .filter-bar {
    margin-bottom: 12px;
  }
  
  .filter-bar :deep(.n-space) {
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .filter-bar :deep(.n-input) {
    min-width: 200px;
    flex: 1;
  }
  
  .status-filter {
    width: 110px;
  }
}

/* 平板直式：縮小元件 */
@media (min-width: 576px) and (max-width: 767px) {
  .filter-bar {
    margin-bottom: 12px;
  }
  
  .filter-bar :deep(.n-space) {
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .filter-bar :deep(.n-input) {
    min-width: 180px;
    flex: 1;
  }
  
  .filter-bar :deep(.n-select) {
    min-width: 100px;
  }
  
  .status-filter {
    width: 100px;
  }
  
  .filter-bar :deep(.n-button) {
    padding: 6px 12px;
    font-size: 0.875rem;
  }
}

/* 手機：垂直排列 */
@media (max-width: 575px) {
  .filter-bar {
    margin-bottom: 8px;
  }
  
  .filter-bar :deep(.n-space) {
    flex-direction: column;
    width: 100%;
    gap: 8px;
  }
  
  .filter-bar :deep(.n-input) {
    width: 100%;
  }
  
  .filter-bar :deep(.n-select) {
    width: 100%;
  }
  
  .status-filter {
    width: 100%;
  }
  
  .filter-bar :deep(.n-button) {
    width: 100%;
    padding: 8px 16px;
    font-size: 0.875rem;
    justify-content: center;
  }
  
  /* 搜索按鈕調整 */
  .filter-bar :deep(.n-input .n-button) {
    width: auto;
    padding: 4px;
  }
}

/* 極小螢幕：進一步優化 */
@media (max-width: 479px) {
  .filter-bar {
    margin-bottom: 6px;
  }
  
  .filter-bar :deep(.n-space) {
    gap: 6px;
  }
  
  .filter-bar :deep(.n-input) {
    font-size: 0.875rem;
  }
  
  .filter-bar :deep(.n-select) {
    font-size: 0.875rem;
  }
  
  .filter-bar :deep(.n-button) {
    font-size: 0.8rem;
    padding: 6px 12px;
  }
}
</style> 