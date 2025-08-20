<template>
  <div class="home-layout">
    <!-- 緊湊式導航區域 -->
    <CompactNavigation 
      :categories="staticDataStore.categories"
      :tags="staticDataStore.tags"
      @apply-filters="handleApplyFilters"
      @clear-all-filters="clearAllFilters"
    />

    <!-- 主要內容區域 -->
    <div class="main-layout">
      <!-- 左側個人介紹 -->
      <ProfileSidebar 
        :stats="{
          totalArticles,
          totalCategories,
          totalTags
        }"
      />

      <!-- 右側文章列表 -->
      <ArticlesSection 
        :articles="articles"
        :loading="loading"
        :error="error"
        :pagination="pagination"
        :current-params="currentParams"
        :categories="staticDataStore.categories"
        :tags="staticDataStore.tags"
        @page-change="changePage"
        @page-size-change="changePageSize"
        @clear-all-filters="clearAllFilters"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import CompactNavigation from '../components/layout/CompactNavigation.vue';
import ProfileSidebar from '../components/profile/ProfileSidebar.vue';
import ArticlesSection from '../components/articles/ArticlesSection.vue';
import { articleApi } from '../api/index';
import { useStaticDataStore } from '../stores/staticData';
import type { Article, ArticleListParams } from '../types/article';
import type { PaginationMeta } from '../types/common';
import { DEFAULT_PAGINATION_PARAMS } from '../constants/pagination';

// Pinia stores
const staticDataStore = useStaticDataStore();

// Article state
const articles = ref<Article[]>([]);
const loading = ref(true);
const error = ref('');
const pagination = ref<PaginationMeta | undefined>(undefined);

const currentParams = ref<ArticleListParams>({
  ...DEFAULT_PAGINATION_PARAMS,
  status: 'published'
});

// 統計數據
const totalArticles = ref(0);
const totalCategories = ref(0);
const totalTags = ref(0);

// 條件性統計數據更新邏輯（分離的職責）
const updateStatsIfNeeded = (params: ArticleListParams, totalItems: number) => {
  // 僅在首頁且無篩選條件時更新總文章數
  if (params.page === 1 && !params.search && !params.category && !params.tags) {
    totalArticles.value = totalItems;
  }
};

// 獲取文章列表（保持單一職責：純粹的數據獲取）
async function fetchArticles() {
  loading.value = true;
  error.value = '';
  
  try {
    const params: ArticleListParams = {
      ...currentParams.value,
      status: 'published'
    };
    
    const response = await articleApi.getList(params);
    articles.value = response.data;
    pagination.value = response.meta?.pagination;
    
    // 分離的統計更新邏輯
    updateStatsIfNeeded(params, response.meta?.pagination?.total_items || 0);
  } catch (err) {
    error.value = '獲取文章列表失敗，請稍後再試';
    console.error(err);
  } finally {
    loading.value = false;
  }
}

// 切換頁碼
function changePage(page: number) {
  currentParams.value.page = page;
  fetchArticles();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// 切換每頁條數
function changePageSize(pageSize: number) {
  currentParams.value.per_page = pageSize;
  currentParams.value.page = 1;
  fetchArticles();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// 統一的參數更新和 API 調用函數
const updateFiltersAndFetch = (updates: Partial<ArticleListParams>) => {
  // 使用展開運算符保持不變性，符合 Vue 響應式最佳實踐
  currentParams.value = {
    ...currentParams.value,
    ...updates,
    page: 1
  };
  fetchArticles();
};

// 舊的個別事件處理函數已移除，統一使用 handleApplyFilters

const clearAllFilters = () => {
  updateFiltersAndFetch({ 
    search: undefined, 
    category: undefined, 
    tags: undefined 
  });
};

// 篩選參數介面定義（加強類型安全）
interface FilterParams {
  search?: string;
  category?: string;
  tags?: string[];
}

// 清理和驗證篩選參數
const cleanFilters = (filters: FilterParams): Partial<ArticleListParams> => {
  const cleaned: Partial<ArticleListParams> = {};
  
  // 搜尋關鍵字：非空字串
  if (filters.search && filters.search.trim()) {
    cleaned.search = filters.search.trim();
  }
  
  // 分類：非空字串
  if (filters.category && filters.category.trim()) {
    cleaned.category = filters.category.trim();
  }
  
  // 標籤：非空陣列
  if (filters.tags && Array.isArray(filters.tags) && filters.tags.length > 0) {
    cleaned.tags = filters.tags.filter(tag => tag && tag.trim());
  }
  
  return cleaned;
};

// 統一篩選處理（解決 CompactNavigation 多事件問題）
const handleApplyFilters = (filters: FilterParams) => {
  const validFilters = cleanFilters(filters);
  updateFiltersAndFetch(validFilters);
};

// 載入靜態統計數據（分類和標籤總數）
async function loadStaticStats() {
  try {
    await staticDataStore.ensureLoaded();
    totalCategories.value = staticDataStore.categories.length;
    totalTags.value = staticDataStore.tags.length;
  } catch (error) {
    console.error('載入靜態統計數據失敗:', error);
  }
}

// 初始化資料載入
onMounted(async () => {
  try {
    // 並行載入靜態數據和文章列表（文章總數會在 fetchArticles 中更新）
    await Promise.all([
      loadStaticStats(),
      fetchArticles()
    ]);
  } catch (error) {
    console.error('初始化資料載入失敗:', error);
  }
});
</script>

<style scoped>
/* 主要佈局 */
.home-layout {
  min-height: 100vh;
  background: var(--background-color);
}

/* 主要內容佈局 */
.main-layout {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 24px;
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 40px;
  align-items: start;
}

/* 響應式設計 - 大幅改善中小螢幕體驗 */
@media (max-width: 1200px) {
  .main-layout {
    max-width: 1000px;
    grid-template-columns: 280px 1fr;
    gap: 28px;
    padding: 28px 20px;
  }
}

@media (max-width: 1024px) {
  .main-layout {
    grid-template-columns: 260px 1fr;
    gap: 24px;
    padding: 24px 16px;
  }
}

@media (max-width: 768px) {
  .main-layout {
    grid-template-columns: 1fr;
    gap: 16px;
    padding: 20px 16px;
  }
}

@media (max-width: 480px) {
  .main-layout {
    padding: 16px 12px;
    gap: 12px;
  }
}
</style>