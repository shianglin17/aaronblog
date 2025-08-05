<template>
  <main class="articles-main">
    <div class="articles-header">
      <h2 class="section-title">
        {{ currentFilter }}
        <span class="articles-count">({{ totalArticles }})</span>
      </h2>
      <div v-if="activeFilter" class="active-filter">
        <span class="filter-text">{{ activeFilterText }}</span>
        <button @click="handleClearAllFilters" class="clear-filter-btn">
          <n-icon size="14"><CloseOutline /></n-icon>
        </button>
      </div>
    </div>

    <div class="articles-content">
      <n-spin :show="loading" description="載入中...">
        <ArticleList 
          :articles="articles" 
          :error="error"
          :pagination="pagination"
          @page-change="handlePageChange"
          @page-size-change="handlePageSizeChange"
        />
      </n-spin>
    </div>
  </main>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { CloseOutline } from '@vicons/ionicons5';
import ArticleList from '../ArticleList.vue';
import type { Article, ArticleListParams } from '../../types/article';
import type { Category } from '../../types/category';
import type { Tag } from '../../types/tag';
import type { PaginationMeta } from '../../types/common';

// Props
const props = defineProps<{
  articles: Article[];
  loading: boolean;
  error: string;
  pagination?: PaginationMeta;
  currentParams: ArticleListParams;
  categories: Category[];
  tags: Tag[];
}>();

// Events
const emit = defineEmits<{
  'page-change': [page: number];
  'page-size-change': [pageSize: number];
  'clear-all-filters': [];
}>();

// 計算屬性
const totalArticles = computed(() => props.pagination?.total_items || 0);

const currentFilter = computed(() => {
  if (props.currentParams.search) return '搜尋結果';
  if (props.currentParams.category) {
    const category = props.categories.find(c => c.slug === props.currentParams.category);
    return category ? `${category.name} 分類` : '分類文章';
  }
  if (props.currentParams.tags && props.currentParams.tags.length > 0) {
    return '標籤文章';
  }
  return '最新文章';
});

const activeFilter = computed(() => {
  return !!(props.currentParams.search || props.currentParams.category || 
           (props.currentParams.tags && props.currentParams.tags.length > 0));
});

const activeFilterText = computed(() => {
  if (props.currentParams.search) return `關鍵字：${props.currentParams.search}`;
  if (props.currentParams.category) {
    const category = props.categories.find(c => c.slug === props.currentParams.category);
    return `分類：${category?.name || props.currentParams.category}`;
  }
  if (props.currentParams.tags && props.currentParams.tags.length > 0) {
    const tagNames = props.currentParams.tags.map(tagSlug => {
      const tag = props.tags.find(t => t.slug === tagSlug);
      return tag?.name || tagSlug;
    });
    return `標籤：${tagNames.join(', ')}`;
  }
  return '';
});

// 事件處理
const handlePageChange = (page: number) => {
  emit('page-change', page);
};

const handlePageSizeChange = (pageSize: number) => {
  emit('page-size-change', pageSize);
};

const handleClearAllFilters = () => {
  emit('clear-all-filters');
};
</script>

<style scoped>
/* 右側文章區域 */
.articles-main {
  min-width: 0;
}

.articles-header {
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--border-color);
}

.section-title {
  font-size: 1.125rem;
  font-weight: 500;
  color: var(--text-color);
  margin-bottom: 8px;
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.articles-count {
  font-size: 0.9rem;
  color: var(--text-secondary);
  font-weight: 400;
}

.active-filter {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: rgba(139, 69, 19, 0.1);
  border-radius: 20px;
  border: 1px solid rgba(139, 69, 19, 0.2);
  margin-top: 8px;
  width: fit-content;
}

.filter-text {
  font-size: 0.85rem;
  color: var(--brand-primary);
  font-weight: 500;
}

.clear-filter-btn {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 2px;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.clear-filter-btn:hover {
  background: rgba(139, 69, 19, 0.2);
  color: var(--brand-primary);
}

.articles-content {
  /* 確保內容區域有足夠空間 */
  min-height: 400px;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .section-title {
    font-size: 1.25rem;
  }
  
  .active-filter {
    padding: 6px 10px;
    margin-top: 12px;
  }
  
  .filter-text {
    font-size: 0.8rem;
  }
}
</style>