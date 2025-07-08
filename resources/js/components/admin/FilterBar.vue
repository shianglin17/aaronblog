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
</style> 