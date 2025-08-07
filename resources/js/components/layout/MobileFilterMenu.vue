<template>
  <transition name="mobile-filter">
    <div v-if="show" class="mobile-filter-overlay" @click="close">
      <div class="mobile-filter-content" @click.stop>
        <div class="filter-header">
          <h3>篩選文章</h3>
          <button @click="close" class="close-button">
            <n-icon size="24"><CloseOutline /></n-icon>
          </button>
        </div>
        
        <div class="filter-body">
          <!-- 搜尋區域 -->
          <div class="search-section">
            <label class="section-label">關鍵字搜尋</label>
            <div class="search-input-wrapper">
              <n-icon size="16" class="search-icon"><SearchOutline /></n-icon>
              <input 
                v-model="localSearchQuery" 
                placeholder="輸入關鍵字..." 
                class="mobile-search-input"
              />
              <button 
                v-if="localSearchQuery" 
                @click="localSearchQuery = ''" 
                class="clear-search"
              >
                <n-icon size="14"><CloseOutline /></n-icon>
              </button>
            </div>
          </div>
          
          <!-- 分類區域 -->
          <div class="category-section">
            <label class="section-label">選擇分類</label>
            <div class="category-grid">
              <button
                @click="localSelectedCategory = ''"
                class="category-item"
                :class="{ 'is-selected': localSelectedCategory === '' }"
              >
                全部分類
              </button>
              <button
                v-for="category in categories"
                :key="category.id"
                @click="localSelectedCategory = category.slug"
                class="category-item"
                :class="{ 'is-selected': localSelectedCategory === category.slug }"
              >
                {{ category.name }}
                <span class="count">({{ category.articles_count || 0 }})</span>
              </button>
            </div>
          </div>
          
          <!-- 標籤區域 -->
          <div class="tags-section">
            <label class="section-label">選擇標籤 (可多選)</label>
            <div class="tags-grid">
              <button
                v-for="tag in tags"
                :key="tag.id"
                @click="toggleTag(tag.slug)"
                class="tag-item"
                :class="{ 'is-selected': localSelectedTags.includes(tag.slug) }"
              >
                {{ tag.name }}
                <span class="count">({{ tag.articles_count || 0 }})</span>
              </button>
            </div>
          </div>
        </div>
        
        <div class="filter-footer">
          <button @click="clearAll" class="footer-button secondary">
            <n-icon size="16"><RefreshOutline /></n-icon>
            重置
          </button>
          <button @click="apply" class="footer-button primary">
            <n-icon size="16"><SearchOutline /></n-icon>
            套用篩選
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { CloseOutline, SearchOutline, RefreshOutline } from '@vicons/ionicons5';
import type { Category } from '../../types/category';
import type { Tag } from '../../types/tag';

// Props
const props = defineProps<{
  show: boolean;
  categories: Category[];
  tags: Tag[];
  searchQuery: string;
  selectedCategory: string;
  selectedTags: string[];
}>();

// Events
const emit = defineEmits<{
  'update:show': [show: boolean];
  'update-search': [query: string];
  'update-category': [slug: string];
  'update-tags': [slugs: string[]];
  'apply-filters': [];
  'clear-filters': [];
}>();

// 本地狀態
const localSearchQuery = ref('');
const localSelectedCategory = ref('');
const localSelectedTags = ref<string[]>([]);

// 監聽 props 變化，同步本地狀態
watch(() => props.searchQuery, (newVal) => {
  localSearchQuery.value = newVal;
}, { immediate: true });

watch(() => props.selectedCategory, (newVal) => {
  localSelectedCategory.value = newVal;
}, { immediate: true });

watch(() => props.selectedTags, (newVal) => {
  localSelectedTags.value = [...newVal];
}, { immediate: true });

// 事件處理
const close = () => {
  emit('update:show', false);
  document.body.style.overflow = '';
};

const toggleTag = (tagSlug: string) => {
  const index = localSelectedTags.value.indexOf(tagSlug);
  if (index > -1) {
    localSelectedTags.value.splice(index, 1);
  } else {
    localSelectedTags.value.push(tagSlug);
  }
};

const apply = () => {
  emit('update-search', localSearchQuery.value);
  emit('update-category', localSelectedCategory.value);
  emit('update-tags', localSelectedTags.value);
  emit('apply-filters');
  close();
};

const clearAll = () => {
  localSearchQuery.value = '';
  localSelectedCategory.value = '';
  localSelectedTags.value = [];
  emit('clear-filters');
};

// 監聽顯示狀態，控制 body 滾動
watch(() => props.show, (show) => {
  if (show) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});
</script>

<style scoped>
/* 手機版篩選選單 */
.mobile-filter-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  z-index: 1000;
  display: flex;
  align-items: flex-end;
}

.mobile-filter-content {
  width: 100%;
  background: var(--surface-elevated);
  border-radius: 16px 16px 0 0;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid var(--border-color);
  background: var(--brand-gradient);
  color: white;
}

.filter-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
}

.close-button {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  transition: var(--transition-fast);
}

.close-button:hover {
  background: rgba(255, 255, 255, 0.2);
}

.filter-body {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
}

.search-section,
.category-section,
.tags-section {
  margin-bottom: 32px;
}

.tags-section {
  margin-bottom: 0;
}

.section-label {
  display: block;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* 搜尋輸入 */
.search-input-wrapper {
  display: flex;
  align-items: center;
  background: var(--surface-color);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 10px 12px;
  gap: 8px;
}

.search-icon {
  color: var(--brand-primary);
  opacity: 0.7;
}

.mobile-search-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 1rem;
  color: var(--text-color);
}

.mobile-search-input::placeholder {
  color: var(--text-tertiary);
}

.clear-search {
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
}

.clear-search:hover {
  background: var(--surface-hover);
  color: var(--brand-primary);
}

/* 分類網格 */
.category-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.category-item {
  background: var(--surface-secondary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 12px;
  text-align: left;
  cursor: pointer;
  transition: var(--transition-normal);
  font-size: 0.9rem;
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.category-item:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
}

.category-item.is-selected {
  background: var(--brand-primary);
  color: white;
  border-color: var(--brand-primary);
}

.count {
  font-size: 0.75rem;
  opacity: 0.7;
  margin-top: 2px;
}

/* 標籤網格 */
.tags-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  gap: 8px;
}

.tag-item {
  background: var(--surface-secondary);
  border: 1px solid var(--border-color);
  border-radius: 20px;
  padding: 8px 12px;
  text-align: center;
  cursor: pointer;
  transition: var(--transition-normal);
  font-size: 0.8rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.tag-item:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
}

.tag-item.is-selected {
  background: var(--brand-primary);
  color: white;
  border-color: var(--brand-primary);
}

/* 底部按鈕 */
.filter-footer {
  display: flex;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid var(--border-color);
  background: var(--surface-secondary);
}

.footer-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition-normal);
}

.footer-button.primary {
  background: var(--brand-primary);
  color: white;
}

.footer-button.primary:hover {
  background: var(--brand-secondary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.footer-button.secondary {
  background: var(--surface-color);
  color: var(--text-secondary);
  border: 1px solid var(--border-color);
}

.footer-button.secondary:hover {
  background: var(--surface-hover);
  color: var(--text-color);
  border-color: var(--text-secondary);
}

/* 動畫 */
.mobile-filter-enter-active,
.mobile-filter-leave-active {
  transition: opacity var(--transition-normal);
}

.mobile-filter-enter-active .mobile-filter-content,
.mobile-filter-leave-active .mobile-filter-content {
  transition: transform var(--transition-normal);
}

.mobile-filter-enter-from,
.mobile-filter-leave-to {
  opacity: 0;
}

.mobile-filter-enter-from .mobile-filter-content,
.mobile-filter-leave-to .mobile-filter-content {
  transform: translateY(100%);
}

/* 響應式調整 */
@media (max-width: 480px) {
  .filter-body {
    padding: 20px;
  }
  
  .category-grid {
    grid-template-columns: 1fr;
  }
  
  .tags-grid {
    grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
    gap: 6px;
  }
  
  .tag-item {
    padding: 6px 10px;
    font-size: 0.75rem;
  }
  
  .filter-footer {
    padding: 16px 20px;
    gap: 10px;
  }
  
  .footer-button {
    padding: 10px 16px;
    font-size: 0.85rem;
  }
}
</style>