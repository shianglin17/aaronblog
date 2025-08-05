<template>
  <main class="articles-main">
    <div class="articles-header">
      <div class="header-main">
        <h2 class="section-title">
          {{ currentFilter }}
          <span class="articles-count">{{ totalArticles }}</span>
        </h2>
        <button 
          v-if="activeFilter" 
          @click="handleClearAllFilters" 
          class="clear-all-button"
          title="清除所有篩選"
        >
          <n-icon size="16"><RefreshOutline /></n-icon>
          重置
        </button>
      </div>
      
      <!-- 簡化的篩選指示器 -->
      <div v-if="activeFilter" class="filter-indicators">
        <span v-if="currentParams.search" class="filter-chip search-chip">
          <n-icon size="12"><SearchOutline /></n-icon>
          {{ currentParams.search }}
        </span>
        <span v-if="currentParams.category" class="filter-chip category-chip">
          <n-icon size="12"><FolderOutline /></n-icon>
          {{ getCategoryName(currentParams.category) }}
        </span>
        <span 
          v-for="tag in (currentParams.tags || [])" 
          :key="tag" 
          class="filter-chip tag-chip"
        >
          <n-icon size="12"><PricetagOutline /></n-icon>
          {{ getTagName(tag) }}
        </span>
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
import { CloseOutline, RefreshOutline, SearchOutline, FolderOutline, PricetagOutline } from '@vicons/ionicons5';
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

// 輔助函數
const getCategoryName = (slug: string): string => {
  const category = props.categories.find(c => c.slug === slug);
  return category ? category.name : slug;
};

const getTagName = (slug: string): string => {
  const tag = props.tags.find(t => t.slug === slug);
  return tag ? tag.name : slug;
};
</script>

<style scoped>
/* 右側文章區域 */
.articles-main {
  min-width: 0;
}

.articles-header {
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--border-color);
}

.header-main {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 500;
  color: var(--text-color);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.articles-count {
  font-size: 0.85rem;
  color: var(--text-secondary);
  font-weight: 400;
  background: var(--surface-secondary);
  padding: 2px 8px;
  border-radius: 12px;
}

.clear-all-button {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: var(--surface-secondary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition-normal);
  font-size: 0.8rem;
  font-weight: 500;
}

.clear-all-button:hover {
  background: var(--surface-hover);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
}

.filter-indicators {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
}

.filter-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 16px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid;
}

.search-chip {
  background: rgba(59, 130, 246, 0.1);
  color: #1e40af;
  border-color: rgba(59, 130, 246, 0.2);
}

.category-chip {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: rgba(139, 69, 19, 0.2);
}

.tag-chip {
  background: rgba(34, 197, 94, 0.1);
  color: #15803d;
  border-color: rgba(34, 197, 94, 0.2);
}

.articles-content {
  /* 確保內容區域有足夠空間 */
  min-height: 400px;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .header-main {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
  }
  
  .section-title {
    font-size: 1.05rem;
    flex: 1;
  }
  
  .articles-count {
    font-size: 0.75rem;
    padding: 2px 6px;
  }
  
  .clear-all-button {
    padding: 4px 8px;
    font-size: 0.75rem;
    flex-shrink: 0;
  }
  
  .filter-indicators {
    gap: 6px;
  }
  
  .filter-chip {
    padding: 3px 8px;
    font-size: 0.7rem;
  }
}

@media (max-width: 480px) {
  .articles-header {
    margin-bottom: 16px;
    padding-bottom: 12px;
  }
  
  .header-main {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }
  
  .section-title {
    font-size: 1rem;
    flex-direction: row;
    align-items: center;
    gap: 6px;
  }
  
  .articles-count {
    font-size: 0.7rem;
    padding: 1px 5px;
  }
  
  .filter-chip {
    padding: 2px 6px;
    font-size: 0.65rem;
  }
}
</style>