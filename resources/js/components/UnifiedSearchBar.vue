<template>
  <div class="search-container">
    <!-- 主搜尋區域 -->
    <div class="search-main" :class="{ 'is-focused': isFocused }">
      <div class="search-input-wrapper">
        <div class="search-icon">
          <n-icon size="20"><SearchOutline /></n-icon>
        </div>
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          placeholder="探索文章的精彩世界..."
          class="search-input"
          @focus="isFocused = true"
          @blur="isFocused = false"
          @keydown.enter="handleSearch"
        />
        <div class="search-actions">
          <transition name="clear-fade">
            <button
              v-if="searchQuery"
              @click="clearSearch"
              class="clear-button"
              type="button"
            >
              <n-icon size="16"><CloseOutline /></n-icon>
            </button>
          </transition>
          <div class="search-divider" v-if="searchQuery"></div>
          <button
            @click="showAdvancedFilters = !showAdvancedFilters"
            class="filter-toggle"
            :class="{ 
              'is-active': showAdvancedFilters,
              'has-filters': selectedCategory || selectedTags.length > 0
            }"
            type="button"
          >
            <n-icon size="18">
              <OptionsOutline v-if="!showAdvancedFilters" />
              <ChevronUpOutline v-else />
            </n-icon>
            <span 
              v-if="(selectedCategory || selectedTags.length > 0) && !showAdvancedFilters" 
              class="filter-indicator"
            ></span>
          </button>
        </div>
      </div>
    </div>



    <!-- 進階篩選面板 -->
    <transition name="filters-expand">
      <div v-if="showAdvancedFilters" class="advanced-panel">
        <div class="filters-grid">
          <div class="filter-group">
            <label class="filter-label">
              <n-icon size="16" class="label-icon"><FolderOutline /></n-icon>
              分類
            </label>
            <n-select
              v-model:value="selectedCategory"
              :options="categoryOptions"
              placeholder="選擇分類"
              clearable
              :disabled="categories.length === 0"
            />
          </div>
          
          <div class="filter-group">
            <label class="filter-label">
              <n-icon size="16" class="label-icon"><PricetagsOutline /></n-icon>
              標籤
            </label>
            <n-select
              v-model:value="selectedTags"
              :options="tagOptions"
              placeholder="選擇標籤"
              multiple
              clearable
              :disabled="tags.length === 0"
              max-tag-count="responsive"
            />
          </div>
        </div>
        
        <div class="panel-actions">
          <button @click="handleSearch" class="action-button primary">
            <n-icon size="16"><SearchOutline /></n-icon>
            <span>開始搜尋</span>
          </button>
          <button @click="clearAllFilters" class="action-button secondary">
            <n-icon size="16"><RefreshOutline /></n-icon>
            <span>重置</span>
          </button>
        </div>
      </div>
    </transition>

    <!-- 內聯篩選標籤 -->
    <transition name="tags-fade">
      <div v-if="hasActiveFilters" class="inline-filters">
        <div 
          v-if="selectedCategory" 
          class="filter-pill category-pill"
        >
          <n-icon size="12"><FolderOutline /></n-icon>
          <span>{{ getCategoryLabel(selectedCategory) }}</span>
          <button @click="clearCategory" class="pill-remove">
            <n-icon size="10"><CloseOutline /></n-icon>
          </button>
        </div>
        
        <div 
          v-for="tag in selectedTags" 
          :key="tag"
          class="filter-pill tag-pill"
        >
          <n-icon size="12"><PricetagOutline /></n-icon>
          <span>{{ getTagLabel(tag) }}</span>
          <button @click="removeTag(tag)" class="pill-remove">
            <n-icon size="10"><CloseOutline /></n-icon>
          </button>
        </div>
        
        <button 
          @click="clearAllFilters" 
          class="clear-all-button"
          title="清除所有篩選"
        >
          <n-icon size="12"><RefreshOutline /></n-icon>
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { 
  SearchOutline, 
  CloseOutline, 
  ChevronUpOutline,
  OptionsOutline,
  FolderOutline,
  PricetagOutline,
  PricetagsOutline,
  RefreshOutline
} from '@vicons/ionicons5';

interface Category {
  id: number;
  name: string;
  slug: string;
}

interface Tag {
  id: number;
  name: string;
  slug: string;
}

interface SearchFilters {
  search?: string;
  category?: string;
  tags?: string[];
}

const props = defineProps<{
  categories: Category[];
  tags: Tag[];
}>();

const emit = defineEmits<{
  'update:filters': [filters: SearchFilters];
}>();

// 搜尋狀態
const searchInput = ref<HTMLInputElement>();
const searchQuery = ref('');
const selectedCategory = ref<string | null>(null);
const selectedTags = ref<string[]>([]);
const showAdvancedFilters = ref(false);
const isFocused = ref(false);



// 計算選項
const categoryOptions = computed(() =>
  props.categories.map((cat) => ({
    label: cat.name,
    value: cat.slug
  }))
);

const tagOptions = computed(() =>
  props.tags.map((tag) => ({
    label: tag.name,
    value: tag.slug
  }))
);

// 是否有活躍的篩選條件
const hasActiveFilters = computed(() => 
  searchQuery.value || selectedCategory.value || selectedTags.value.length > 0
);



// 執行搜尋
const handleSearch = () => {
  const filters: SearchFilters = {};
  
  if (searchQuery.value.trim()) {
    filters.search = searchQuery.value.trim();
  }
  
  if (selectedCategory.value) {
    filters.category = selectedCategory.value;
  }
  
  if (selectedTags.value.length > 0) {
    filters.tags = selectedTags.value;
  }
  
  emit('update:filters', filters);
  
  // 收合進階面板
  if (showAdvancedFilters.value) {
    showAdvancedFilters.value = false;
  }
  
  // 移除焦點
  searchInput.value?.blur();
};

// 清除所有篩選
const clearAllFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = null;
  selectedTags.value = [];
  emit('update:filters', {});
};

// 清除搜尋
const clearSearch = () => {
  searchQuery.value = '';
  handleSearch();
};

// 清除分類
const clearCategory = () => {
  selectedCategory.value = null;
  handleSearch();
};

// 移除標籤
const removeTag = (tagSlug: string) => {
  selectedTags.value = selectedTags.value.filter(tag => tag !== tagSlug);
  handleSearch();
};

// 獲取分類標籤
const getCategoryLabel = (slug: string): string => {
  const category = props.categories.find(cat => cat.slug === slug);
  return category ? category.name : slug;
};

// 獲取標籤標籤
const getTagLabel = (slug: string): string => {
  const tag = props.tags.find(t => t.slug === slug);
  return tag ? tag.name : slug;
};

// 監聽外部資料變化，重置選擇
watch(
  () => [props.categories, props.tags],
  () => {
    selectedCategory.value = null;
    selectedTags.value = [];
  }
);
</script>

<style scoped>
.search-container {
  max-width: 700px;
  margin: 0 auto 32px;
  position: relative;
}

/* 主搜尋區域 */
.search-main {
  background: var(--search-background);
  border: var(--search-border);
  border-radius: var(--search-border-radius);
  overflow: hidden;
  transition: var(--card-transition);
  box-shadow: var(--search-shadow);
  position: relative;
}

.search-main.is-focused {
  border-color: var(--primary-color);
  box-shadow: var(--search-shadow-focus);
  transform: translateY(-2px);
}

.search-input-wrapper {
  display: flex;
  align-items: center;
  padding: var(--search-padding);
  gap: 16px;
  min-height: var(--search-input-height);
}

.search-icon {
  color: #8b8680;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
}

.search-main.is-focused .search-icon {
  color: var(--primary-color);
}

.search-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 16px;
  color: var(--text-color);
  line-height: 1.5;
  font-weight: 400;
  letter-spacing: 0.02em;
}

.search-input::placeholder {
  color: #a8a29e;
  font-weight: 300;
}

.search-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.clear-button,
.filter-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  background: transparent;
  color: #78716c;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.clear-button:hover {
  background: #f5f5f4;
  color: #ef4444;
}

.filter-toggle:hover {
  background: #f5f5f4;
  color: var(--primary-color);
}

.filter-toggle.is-active {
  background: var(--primary-color);
  color: white;
}

.filter-toggle.has-filters {
  background: rgba(251, 191, 36, 0.1);
  color: #92400e;
  border: 1px solid rgba(251, 191, 36, 0.3);
}

.filter-toggle.has-filters:hover {
  background: rgba(251, 191, 36, 0.15);
}

.filter-indicator {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 8px;
  height: 8px;
  background: #dc2626;
  border-radius: 50%;
  border: 1px solid white;
}

.search-divider {
  width: 1px;
  height: 20px;
  background: #e5e7eb;
}



/* 進階篩選面板 */
.advanced-panel {
  background: linear-gradient(135deg, #fafafa 0%, #f5f5f4 100%);
  border: 2px solid #e8e5e0;
  border-top: none;
  border-radius: 0 0 16px 16px;
  padding: 24px;
  margin-top: -2px;
}

.filters-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.filter-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-color);
}

.label-icon {
  color: var(--primary-color);
}

.panel-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 120px;
  justify-content: center;
}

.action-button.primary {
  background: var(--primary-color);
  color: white;
}

.action-button.primary:hover {
  background: var(--primary-hover);
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(125, 110, 93, 0.3);
}

.action-button.secondary {
  background: white;
  color: #6b7280;
  border: 2px solid #e5e7eb;
}

.action-button.secondary:hover {
  background: #f9fafb;
  border-color: #d1d5db;
  transform: translateY(-1px);
}

/* 內聯篩選標籤 */
.inline-filters {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}

.filter-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.category-pill {
  background: rgba(251, 191, 36, 0.1);
  color: #92400e;
  border: 1px solid rgba(251, 191, 36, 0.3);
}

.category-pill:hover {
  background: rgba(251, 191, 36, 0.15);
  border-color: rgba(251, 191, 36, 0.4);
}

.tag-pill {
  background: rgba(110, 231, 183, 0.1);
  color: #065f46;
  border: 1px solid rgba(110, 231, 183, 0.3);
}

.tag-pill:hover {
  background: rgba(110, 231, 183, 0.15);
  border-color: rgba(110, 231, 183, 0.4);
}

.pill-remove {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  border: none;
  border-radius: 50%;
  background: transparent;
  color: currentColor;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-left: 2px;
}

.pill-remove:hover {
  background: rgba(0, 0, 0, 0.1);
  transform: scale(1.2);
}

.clear-all-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border: none;
  border-radius: 50%;
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-left: 4px;
}

.clear-all-button:hover {
  background: rgba(239, 68, 68, 0.15);
  transform: scale(1.1);
}

/* 動畫效果 */
.clear-fade-enter-active,
.clear-fade-leave-active {
  transition: all 0.2s ease;
}

.clear-fade-enter-from,
.clear-fade-leave-to {
  opacity: 0;
  transform: scale(0.8);
}



.filters-expand-enter-active,
.filters-expand-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.filters-expand-enter-from,
.filters-expand-leave-to {
  opacity: 0;
  transform: translateY(-12px);
  max-height: 0;
}

.filters-expand-enter-to,
.filters-expand-leave-from {
  opacity: 1;
  transform: translateY(0);
  max-height: 400px;
}

.tags-fade-enter-active,
.tags-fade-leave-active {
  transition: all 0.25s ease;
}

.tags-fade-enter-from,
.tags-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* 響應式設計 */
@media (max-width: 768px) {
  .search-container {
    margin: 8px 16px;
  }
  
  .search-input-wrapper {
    padding: 10px 16px;
    gap: 12px;
  }
  
  .search-input {
    font-size: 16px; /* iOS 防縮放 */
  }
  
  .advanced-panel {
    padding: 20px;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .panel-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .action-button {
    min-width: auto;
  }
  
  .inline-filters {
    gap: 6px;
    margin-top: 8px;
  }
  
  .filter-pill {
    font-size: 11px;
    padding: 4px 8px;
  }
}

@media (max-width: 480px) {
  .search-main {
    border-radius: 12px;
  }
  
  .search-input-wrapper {
    padding: 4px 14px;
    gap: 10px;
  }
  
  .advanced-panel {
    padding: 16px;
    border-radius: 0 0 12px 12px;
  }
  

}
</style> 