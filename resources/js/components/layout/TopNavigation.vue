<template>
  <nav class="top-navigation">
    <div class="nav-container">
      <!-- 品牌標誌 - 增加互動效果 -->
      <div class="brand-logo">
        <h1 class="brand-name">
          <span class="brand-text">Aaron</span><span class="brand-accent">Blog</span>
          <div class="brand-underline"></div>
        </h1>
      </div>

      <!-- 簡單搜尋框 -->
      <div class="search-box">
        <n-icon size="16" class="search-icon"><SearchOutline /></n-icon>
        <input 
          v-model="searchQuery" 
          @keydown.enter="handleSearch"
          @input="handleSearchInput"
          placeholder="搜尋文章..." 
          class="search-input"
        />
        <button 
          v-if="searchQuery" 
          @click="clearSearch" 
          class="clear-button"
        >
          <n-icon size="14"><CloseOutline /></n-icon>
        </button>
      </div>

      <!-- 響應式導航選單 -->
      <div class="nav-menu">
        <!-- 桌面版導航 -->
        <div class="desktop-nav">
          <div class="nav-item" @mouseenter="showCategories = true" @mouseleave="showCategories = false">
            <span class="nav-text">分類</span>
            <n-icon size="14" class="nav-arrow"><ChevronDownOutline /></n-icon>
            
            <!-- 分類下拉選單 -->
            <transition name="dropdown">
              <div v-if="showCategories" class="dropdown-menu categories-menu">
                <div class="dropdown-header">文章分類</div>
                <a 
                  v-for="category in categories" 
                  :key="category.id"
                  @click="handleCategoryFilter(category.slug)"
                  class="dropdown-item"
                >
                  {{ category.name }}
                  <span class="item-count">{{ category.articles_count || 0 }}</span>
                </a>
                <a @click="handleClearCategoryFilter" class="dropdown-item clear-filter">
                  全部分類
                </a>
              </div>
            </transition>
          </div>

          <div class="nav-item" @mouseenter="showTags = true" @mouseleave="showTags = false">
            <span class="nav-text">標籤</span>
            <n-icon size="14" class="nav-arrow"><ChevronDownOutline /></n-icon>
            
            <!-- 標籤下拉選單 -->
            <transition name="dropdown">
              <div v-if="showTags" class="dropdown-menu tags-menu">
                <div class="dropdown-header">熱門標籤</div>
                <div class="tags-grid">
                  <button 
                    v-for="tag in tags.slice(0, 12)" 
                    :key="tag.id"
                    @click="handleTagFilter(tag.slug)"
                    class="tag-chip"
                  >
                    {{ tag.name }}
                  </button>
                </div>
                <a @click="handleClearTagFilter" class="dropdown-item clear-filter">
                  全部標籤
                </a>
              </div>
            </transition>
          </div>
        </div>
        
        <!-- 手機版選單按鈕 -->
        <div class="mobile-nav">
          <button @click="toggleMobileMenu" class="mobile-menu-btn">
            <n-icon size="20"><MenuOutline /></n-icon>
            <span>選單</span>
          </button>
        </div>
      </div>
    </div>
  </nav>
  
  <!-- 手機版全螢幕選單 -->
  <MobileMenu 
    v-model:show="showMobileMenu"
    :categories="categories"
    :tags="tags"
    @category-filter="handleCategoryFilter"
    @tag-filter="handleTagFilter"
    @clear-category-filter="handleClearCategoryFilter"
    @clear-tag-filter="handleClearTagFilter"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { SearchOutline, CloseOutline, ChevronDownOutline, MenuOutline } from '@vicons/ionicons5';
import MobileMenu from './MobileMenu.vue';
import type { Category } from '../../types/category';
import type { Tag } from '../../types/tag';

// Props
defineProps<{
  categories: Category[];
  tags: Tag[];
}>();

// Events
const emit = defineEmits<{
  search: [query: string];
  'search-input': [query: string];
  'clear-search': [];
  'category-filter': [slug: string];
  'tag-filter': [slug: string];
  'clear-category-filter': [];
  'clear-tag-filter': [];
}>();

// 狀態管理
const searchQuery = ref('');
const showCategories = ref(false);
const showTags = ref(false);
const showMobileMenu = ref(false);

// 事件處理
const handleSearch = () => {
  emit('search', searchQuery.value.trim());
};

const handleSearchInput = () => {
  emit('search-input', searchQuery.value);
};

const clearSearch = () => {
  searchQuery.value = '';
  emit('clear-search');
};

const handleCategoryFilter = (slug: string) => {
  showCategories.value = false;
  emit('category-filter', slug);
};

const handleTagFilter = (slug: string) => {
  showTags.value = false;
  emit('tag-filter', slug);
};

const handleClearCategoryFilter = () => {
  showCategories.value = false;
  emit('clear-category-filter');
};

const handleClearTagFilter = () => {
  showTags.value = false;
  emit('clear-tag-filter');
};

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  if (showMobileMenu.value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
};
</script>

<style scoped>
/* 頂部導航 - 現代化設計 */
.top-navigation {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border-color);
  padding: 20px 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow-sm);
  transition: var(--transition-normal);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 32px;
}

/* 品牌標誌 - 互動設計 */
.brand-logo {
  flex: 0 0 auto;
  cursor: pointer;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.02em;
  position: relative;
  transition: var(--transition-normal);
  display: flex;
  align-items: center;
  gap: 2px;
}

.brand-text {
  background: var(--brand-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  transition: var(--transition-normal);
}

.brand-accent {
  color: var(--brand-secondary);
  transition: var(--transition-normal);
}

.brand-underline {
  position: absolute;
  bottom: -4px;
  left: 0;
  height: 2px;
  width: 0;
  background: var(--brand-gradient);
  transition: var(--transition-normal);
  border-radius: 1px;
}

.brand-name:hover .brand-underline {
  width: 100%;
}

.brand-name:hover {
  transform: translateY(-1px);
}

.brand-name:active {
  transform: translateY(0) scale(var(--active-scale));
}

/* 搜尋框 - 現代化設計 */
.search-box {
  display: flex;
  align-items: center;
  background: var(--surface-color);
  border: 2px solid var(--border-color);
  border-radius: 12px;
  padding: 12px 16px;
  width: 320px;
  flex: 0 0 auto;
  box-shadow: var(--shadow-xs);
  transition: var(--transition-normal);
}

.search-box:focus-within {
  border-color: var(--brand-primary);
  box-shadow: var(--shadow-md), 0 0 0 3px rgba(139, 69, 19, 0.1);
  transform: translateY(-1px);
}

.search-icon {
  color: var(--brand-primary);
  margin-right: 10px;
  opacity: 0.8;
}

.search-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 0.9rem;
  color: var(--text-color);
  font-weight: 400;
}

.search-input::placeholder {
  color: var(--text-tertiary);
}

.clear-button {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 4px;
  margin-left: 8px;
}

.clear-button:hover {
  color: var(--brand-primary);
}

/* 響應式導航選單 */
.nav-menu {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.desktop-nav {
  display: flex;
  gap: 8px;
}

.mobile-nav {
  display: none;
}

.nav-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  cursor: pointer;
  border-radius: 8px;
  user-select: none;
  color: var(--text-secondary);
  background: var(--surface-color);
  border: 1px solid var(--border-color);
  transition: var(--transition-normal);
  white-space: nowrap;
  min-width: 0;
  flex-shrink: 0;
}

.nav-item:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.nav-text {
  font-size: 0.9rem;
  font-weight: 500;
}

.nav-arrow {
  opacity: 0.6;
}

/* 下拉選單 - 智能定位防破版 */
.dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  background: var(--surface-elevated);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  padding: 12px 0;
  width: 280px;
  max-width: calc(100vw - 32px);
  z-index: 200;
  backdrop-filter: blur(12px);
  overflow: hidden;
}

/* 分類選單 - 左對齊 */
.categories-menu {
  left: 0;
  right: auto;
}

/* 標籤選單 - 右對齊防破版 */
.tags-menu {
  left: auto;
  right: 0;
}

/* 在小螢幕上統一居中 */
@media (max-width: 1024px) {
  .dropdown-menu {
    left: 50% !important;
    right: auto !important;
    transform: translateX(-50%);
    width: 300px;
  }
}

.dropdown-header {
  padding: 8px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-secondary);
  border-bottom: 1px solid var(--border-color);
  margin-bottom: 4px;
}

.dropdown-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 12px;
  color: var(--text-color);
  text-decoration: none;
  cursor: pointer;
  font-size: 0.85rem;
}

.dropdown-item:hover {
  background: var(--surface-hover);
  color: var(--brand-primary);
}

.item-count {
  font-size: 0.75rem;
  color: var(--text-secondary);
  background: var(--surface-secondary);
  padding: 1px 4px;
  border-radius: 4px;
}

.clear-filter {
  border-top: 1px solid var(--border-color);
  margin-top: 4px;
  font-weight: 500;
  color: var(--text-secondary);
}

/* 標籤網格 - 完全修正破版 */
.tags-menu {
  min-width: 280px;
  max-width: 380px;
}

.tags-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
  gap: 6px;
  padding: 12px 16px;
  margin-bottom: 12px;
  max-height: 200px;
  overflow-y: auto;
}

.tag-chip {
  background: var(--surface-secondary);
  border: 1px solid var(--border-color);
  border-radius: 20px;
  padding: 6px 12px;
  font-size: 0.75rem;
  color: var(--brand-primary);
  cursor: pointer;
  text-align: center;
  font-weight: 500;
  white-space: nowrap;
  transition: var(--transition-normal);
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}

.tag-chip:hover {
  background: var(--brand-primary);
  color: white;
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

/* 簡化動畫 */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
}

/* 手機版選單按鈕 */
.mobile-menu-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  background: var(--surface-color);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition-normal);
  font-size: 0.9rem;
  font-weight: 500;
}

.mobile-menu-btn:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

/* 響應式設計 - 改善層次感 */
@media (max-width: 1024px) {
  .nav-container {
    padding: 0 20px;
    gap: 20px;
  }
  
  .search-box {
    width: 280px;
  }
  
  .nav-menu {
    gap: 6px;
  }
}

@media (max-width: 768px) {
  .top-navigation {
    padding: 16px 0;
  }
  
  .nav-container {
    flex-direction: column;
    gap: 16px;
    padding: 0 16px;
    align-items: stretch;
  }
  
  .brand-logo {
    align-self: center;
  }
  
  .desktop-nav {
    display: none;
  }
  
  .mobile-nav {
    display: flex;
    justify-content: center;
  }
  
  .search-box {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
  }
}

@media (max-width: 480px) {
  .brand-name {
    font-size: 1.25rem;
  }
  
  .search-box {
    padding: 10px 14px;
  }
}
</style>