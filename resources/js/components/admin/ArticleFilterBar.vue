<template>
  <div class="article-filter-bar">
    <!-- 基本篩選 -->
    <FilterBar
      v-model:search="searchKeyword"
      v-model:status="statusFilter"
      :show-status="true"
      @search="handleSearch"
      @status-change="handleStatusChange"
    >
      <template #suffix>
        <n-button quaternary @click="toggleAdvancedFilters">
          進階篩選
          <n-icon>
            <filter-outline />
          </n-icon>
        </n-button>
        
        <slot name="suffix"></slot>
      </template>
    </FilterBar>
    
    <!-- 進階篩選面板 -->
    <n-collapse-transition :show="showAdvancedFilters">
      <div class="advanced-filters">
        <n-card size="small" title="進階篩選">
          <n-space vertical>
            <n-form-item label="分類">
              <n-select
                v-model:value="categoryFilter"
                :options="categoryOptions"
                placeholder="選擇分類"
                clearable
              />
            </n-form-item>
            
            <n-form-item label="標籤">
              <n-select
                v-model:value="tagFilters"
                :options="tagOptions"
                placeholder="選擇標籤"
                multiple
                clearable
              />
            </n-form-item>
            
            <div class="filter-actions">
              <n-space justify="end">
                <n-button size="small" @click="resetFilters">重置</n-button>
                <n-button type="primary" size="small" @click="applyFilters">套用</n-button>
              </n-space>
            </div>
          </n-space>
        </n-card>
      </div>
    </n-collapse-transition>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { FilterOutline } from '@vicons/ionicons5';
import FilterBar from './FilterBar.vue';
import type { SelectOption } from 'naive-ui';

// 定義屬性
const props = defineProps({
  // 搜索關鍵字
  search: {
    type: String,
    default: ''
  },
  
  // 狀態篩選值
  status: {
    type: String,
    default: 'all'
  },
  
  // 分類篩選值
  category: {
    type: String,
    default: undefined
  },
  
  // 標籤篩選值
  tags: {
    type: Array as () => string[],
    default: () => []
  },
  
  // 分類選項
  categoryOptions: {
    type: Array as () => SelectOption[],
    default: () => []
  },
  
  // 標籤選項
  tagOptions: {
    type: Array as () => SelectOption[],
    default: () => []
  }
});

// 定義事件
const emit = defineEmits<{
  (e: 'search', value: string): void;
  (e: 'status-change', value: string): void;
  (e: 'category-change', value: string | undefined): void;
  (e: 'tags-change', value: string[]): void;
  (e: 'reset'): void;
  (e: 'apply-filters'): void; // 新增套用篩選事件
  (e: 'update:search', value: string): void;
  (e: 'update:status', value: string): void;
  (e: 'update:category', value: string | undefined): void;
  (e: 'update:tags', value: string[]): void;
}>();

// 本地狀態
const searchKeyword = ref(props.search);
const statusFilter = ref(props.status);
const categoryFilter = ref(props.category);
const tagFilters = ref(props.tags);
const showAdvancedFilters = ref(false);

// 監聽外部屬性變化
watch(() => props.search, (newVal) => {
  searchKeyword.value = newVal;
});

watch(() => props.status, (newVal) => {
  statusFilter.value = newVal;
});

watch(() => props.category, (newVal) => {
  categoryFilter.value = newVal;
});

watch(() => props.tags, (newVal) => {
  tagFilters.value = newVal;
});

// 處理搜索
function handleSearch(value: string) {
  emit('update:search', value);
  emit('search', value);
}

// 處理狀態變化
function handleStatusChange(value: string) {
  emit('update:status', value);
  emit('status-change', value);
}

// 分類和標籤變化現在統一通過 applyFilters 觸發，提供更一致的使用體驗

// 重置所有篩選
function resetFilters() {
  searchKeyword.value = '';
  statusFilter.value = 'all';
  categoryFilter.value = undefined;
  tagFilters.value = [];
  
  emit('update:search', '');
  emit('update:status', 'all');
  emit('update:category', undefined);
  emit('update:tags', []);
  emit('reset');
}

// 套用篩選
function applyFilters() {
  // 更新 v-model 值
  emit('update:search', searchKeyword.value);
  emit('update:status', statusFilter.value);
  emit('update:category', categoryFilter.value);
  emit('update:tags', tagFilters.value);
  
  // 發出統一的套用事件給父元件處理
  // 移除個別的篩選變更事件觸發，避免重複 API 請求
  emit('apply-filters');
  
  // 收起進階篩選面板
  showAdvancedFilters.value = false;
}

// 切換進階篩選
function toggleAdvancedFilters() {
  showAdvancedFilters.value = !showAdvancedFilters.value;
}
</script>

<style scoped>
.article-filter-bar {
  margin-bottom: 20px;
}

.advanced-filters {
  margin: 16px 0;
}

.filter-actions {
  margin-top: 12px;
}
</style> 