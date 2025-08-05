<template>
  <nav class="compact-navigation">
    <div class="nav-container">
      <!-- 品牌標誌 -->
      <div class="brand-logo">
        <h1 class="brand-name">
          <span class="brand-text">Aaron</span><span class="brand-accent">Blog</span>
        </h1>
      </div>

      <!-- 桌面版：整合搜尋篩選欄 -->
      <div class="desktop-search">
        <div class="search-wrapper">
          <div class="search-input-container">
            <n-icon size="16" class="search-icon"><SearchOutline /></n-icon>
            <input 
              v-model="searchQuery" 
              @keydown.enter="handleSearch"
              placeholder="搜尋文章..." 
              class="search-input"
            />
            <button 
              v-if="searchQuery" 
              @click="clearSearch" 
              class="clear-button"
            >
              <n-icon size="12"><CloseOutline /></n-icon>
            </button>
          </div>
          
          <!-- 篩選按鈕 -->
          <button 
            @click="showFilters = !showFilters"
            class="filter-button"
            :class="{ 
              'has-filters': hasActiveFilters,
              'is-active': showFilters 
            }"
          >
            <n-icon size="16"><OptionsOutline /></n-icon>
            <span v-if="hasActiveFilters" class="filter-indicator"></span>
          </button>
        </div>

        <!-- 篩選面板 -->
        <transition name="filter-panel">
          <div v-if="showFilters" class="filter-panel">
            <div class="filter-section">
              <label class="filter-label">分類</label>
              <select 
                v-model="selectedCategory" 
                @change="handleCategoryChange"
                class="filter-select"
              >
                <option value="">全部分類</option>
                <option 
                  v-for="category in categories" 
                  :key="category.id"
                  :value="category.slug"
                >
                  {{ category.name }} ({{ category.articles_count || 0 }})
                </option>
              </select>
            </div>
            
            <div class="filter-section">
              <label class="filter-label">標籤</label>
              <div class="tags-container">
                <button
                  v-for="tag in tags.slice(0, 8)"
                  :key="tag.id"
                  @click="toggleTag(tag.slug)"
                  class="tag-button"
                  :class="{ 'is-selected': selectedTags.includes(tag.slug) }"
                >
                  {{ tag.name }}
                </button>
              </div>
            </div>
            
            <div class="filter-actions">
              <button @click="applyFilters" class="apply-button">
                <n-icon size="14"><SearchOutline /></n-icon>
                搜尋
              </button>
              <button @click="clearAllFilters" class="clear-button-action">
                <n-icon size="14"><RefreshOutline /></n-icon>
                重置
              </button>
            </div>
          </div>
        </transition>
      </div>

      <!-- 手機版：漢堡選單 -->
      <div class="mobile-menu">
        <button @click="showMobileMenu = true" class="hamburger-button">
          <n-icon size="20"><MenuOutline /></n-icon>
        </button>
      </div>
    </div>

    <!-- 移除活躍篩選條件顯示，避免重複 -->
  </nav>

  <!-- 手機版全螢幕選單 -->
  <MobileFilterMenu 
    v-model:show="showMobileMenu"
    :categories="categories"
    :tags="tags"
    :search-query="searchQuery"
    :selected-category="selectedCategory"
    :selected-tags="selectedTags"
    @update-search="searchQuery = $event"
    @update-category="selectedCategory = $event"
    @update-tags="selectedTags = $event"
    @apply-filters="applyFilters"
    @clear-filters="clearAllFilters"
  />
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { SearchOutline, CloseOutline, OptionsOutline, MenuOutline, RefreshOutline } from '@vicons/ionicons5';
import MobileFilterMenu from './MobileFilterMenu.vue';
import type { Category } from '../../types/category';
import type { Tag } from '../../types/tag';

// Props
const props = defineProps<{
  categories: Category[];
  tags: Tag[];
}>();

// Events
const emit = defineEmits<{
  search: [query: string];
  'category-filter': [slug: string];
  'tag-filter': [slugs: string[]];
  'clear-search': [];
  'clear-category-filter': [];
  'clear-tag-filter': [];
  'clear-all-filters': [];
}>();

// 狀態管理
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedTags = ref<string[]>([]);
const showFilters = ref(false);
const showMobileMenu = ref(false);

// 計算屬性
const hasActiveFilters = computed(() => 
  searchQuery.value || selectedCategory.value || selectedTags.value.length > 0
);

// 事件處理
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    emit('search', searchQuery.value.trim());
  }
  showFilters.value = false;
};

const handleCategoryChange = () => {
  if (selectedCategory.value) {
    emit('category-filter', selectedCategory.value);
  } else {
    emit('clear-category-filter');
  }
};

const toggleTag = (tagSlug: string) => {
  const index = selectedTags.value.indexOf(tagSlug);
  if (index > -1) {
    selectedTags.value.splice(index, 1);
  } else {
    selectedTags.value.push(tagSlug);
  }
};

const applyFilters = () => {
  if (searchQuery.value.trim()) {
    emit('search', searchQuery.value.trim());
  }
  if (selectedCategory.value) {
    emit('category-filter', selectedCategory.value);
  }
  if (selectedTags.value.length > 0) {
    emit('tag-filter', selectedTags.value);
  }
  showFilters.value = false;
  showMobileMenu.value = false;
};

const clearSearch = () => {
  searchQuery.value = '';
  emit('clear-search');
};

const clearAllFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  selectedTags.value = [];
  emit('clear-all-filters');
  showFilters.value = false;
  showMobileMenu.value = false;
};
</script>

<style scoped>
/* 緊湊式導航 */
.compact-navigation {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border-color);
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow-sm);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 12px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

/* 品牌標誌 - 簡化版 */
.brand-logo {
  flex: 0 0 auto;
}

.brand-name {
  font-size: 1.25rem;
  font-weight: 800;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 1px;
}

.brand-text {
  background: var(--brand-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-accent {
  color: var(--brand-secondary);
}

/* 桌面版搜尋區域 */
.desktop-search {
  flex: 1;
  max-width: 600px;
  position: relative;
}

.search-wrapper {
  display: flex;
  align-items: center;
  background: var(--surface-color);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
  transition: var(--transition-normal);
}

.search-wrapper:focus-within {
  border-color: var(--brand-primary);
  box-shadow: 0 0 0 2px rgba(139, 69, 19, 0.1);
}

.search-input-container {
  flex: 1;
  display: flex;
  align-items: center;
  padding: 8px 12px;
  gap: 8px;
}

.search-icon {
  color: var(--brand-primary);
  opacity: 0.7;
}

.search-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 0.9rem;
  color: var(--text-color);
}

.search-input::placeholder {
  color: var(--text-tertiary);
}

.clear-button {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 2px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  transition: var(--transition-fast);
}

.clear-button:hover {
  background: var(--surface-hover);
  color: var(--brand-primary);
}

/* 篩選按鈕 */
.filter-button {
  background: none;
  border: none;
  border-left: 1px solid var(--border-color);
  padding: 10px 12px;
  cursor: pointer;
  color: var(--text-secondary);
  transition: var(--transition-normal);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.filter-button:hover {
  background: var(--surface-hover);
  color: var(--brand-primary);
}

.filter-button.is-active {
  background: var(--brand-light);
  color: var(--brand-primary);
}

.filter-button.has-filters {
  color: var(--brand-primary);
  background: var(--brand-light);
}

.filter-indicator {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 6px;
  height: 6px;
  background: var(--brand-secondary);
  border-radius: 50%;
}

/* 篩選面板 */
.filter-panel {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: var(--surface-elevated);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  padding: 16px;
  z-index: 200;
}

.filter-section {
  margin-bottom: 16px;
}

.filter-section:last-of-type {
  margin-bottom: 12px;
}

.filter-label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filter-select {
  width: 100%;
  padding: 6px 8px;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--surface-color);
  color: var(--text-color);
  font-size: 0.85rem;
}

.tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.tag-button {
  background: var(--surface-secondary);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 4px 10px;
  font-size: 0.75rem;
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition-fast);
}

.tag-button:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
}

.tag-button.is-selected {
  background: var(--brand-primary);
  color: white;
  border-color: var(--brand-primary);
}

.filter-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  border-top: 1px solid var(--border-color);
  padding-top: 12px;
}

.apply-button,
.clear-button-action {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition-normal);
}

.apply-button {
  background: var(--brand-primary);
  color: white;
}

.apply-button:hover {
  background: var(--brand-secondary);
}

.clear-button-action {
  background: var(--surface-secondary);
  color: var(--text-secondary);
}

.clear-button-action:hover {
  background: var(--surface-hover);
  color: var(--text-color);
}

/* 手機版選單 */
.mobile-menu {
  display: none;
}

.hamburger-button {
  background: none;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  padding: 8px;
  cursor: pointer;
  color: var(--text-secondary);
  transition: var(--transition-normal);
}

.hamburger-button:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
}

/* 活躍篩選條件已移除，避免 UI 重複 */

/* 動畫 */
.filter-panel-enter-active,
.filter-panel-leave-active {
  transition: all 0.2s ease;
}

.filter-panel-enter-from,
.filter-panel-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* 響應式設計 */
@media (max-width: 768px) {
  .nav-container {
    padding: 10px 16px;
    gap: 16px;
  }
  
  .desktop-search {
    display: none;
  }
  
  .mobile-menu {
    display: block;
  }
  
  .brand-name {
    font-size: 1.1rem;
  }
}

@media (max-width: 480px) {
  .nav-container {
    padding: 8px 12px;
  }
  
  .brand-name {
    font-size: 1rem;
  }
}
</style>