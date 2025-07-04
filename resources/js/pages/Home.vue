<template>
  <div class="container">
    <header class="header">
      <h1 class="site-title">Aaron 的部落格</h1>
      <p class="site-description">軟體開發紀錄、生活分享</p>
    </header>

    <!-- 統一搜尋器 -->
    <UnifiedSearchBar
      :categories="categories"
      :tags="tags"
      @update:filters="handleFilterChange"
    />
  
    <!-- 文章列表 -->
    <n-spin :show="loading" description="載入中...">
      <ArticleList 
        :articles="articles" 
        :error="error"
        :pagination="pagination"
        @page-change="changePage"
        @page-size-change="changePageSize"
      />
    </n-spin>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import ArticleList from '../components/ArticleList.vue';
import UnifiedSearchBar from '../components/UnifiedSearchBar.vue';
import { articleApi, tagApi } from '../api/index';
import type { Article, ArticleListParams } from '../types/article';
import type { Category } from '../types/category';
import type { Tag } from '../types/tag';
import type { PaginationMeta } from '../types/common';
import { DEFAULT_PAGINATION_PARAMS } from '../constants/pagination';

// 狀態管理
const articles = ref<Article[]>([]);
const loading = ref(true);
const error = ref('');
const pagination = ref<PaginationMeta | undefined>(undefined);

// 篩選狀態
const categories = ref<Category[]>([]);
const tags = ref<Tag[]>([]);
const filtersLoading = ref(true);

const currentParams = ref<ArticleListParams>({
  ...DEFAULT_PAGINATION_PARAMS,
  status: 'published'
});

// 獲取文章列表
async function fetchArticles() {
  loading.value = true;
  error.value = '';
  
  try {
    // 確保請求參數中總是包含 status=published
    const params: ArticleListParams = {
      ...currentParams.value,
      status: 'published' // 強制覆蓋，確保前台只顯示已發布文章
    };
    
    const response = await articleApi.getList(params);
    articles.value = response.data;
    pagination.value = response.meta?.pagination;
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
  // 頁面滾動到頂部
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// 切換每頁條數
function changePageSize(pageSize: number) {
  currentParams.value.per_page = pageSize;
  currentParams.value.page = 1; // 重置頁碼
  fetchArticles();
  // 頁面滾動到頂部
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// 處理篩選變更（整合搜尋和篩選）
function handleFilterChange(filters: { search?: string, category?: string, tags?: string[] }) {
  currentParams.value.search = filters.search;
  currentParams.value.category = filters.category || undefined;
  currentParams.value.tags = filters.tags;
  currentParams.value.page = 1; // 重置頁碼
  fetchArticles();
}

// 獲取分類和標籤數據
async function fetchFilters() {
  filtersLoading.value = true;
  
  try {
    // 並行請求以提高效率
    const [categoriesResponse, tagsResponse] = await Promise.all([
      articleApi.getAllCategories(),
      tagApi.getList()
    ]);
    
    categories.value = categoriesResponse.data;
    tags.value = tagsResponse.data;
  } catch (err) {
    console.error('獲取篩選數據失敗:', err);
  } finally {
    filtersLoading.value = false;
  }
}

// 掛載時獲取文章
onMounted(() => {
  fetchArticles();
  fetchFilters();
});
</script>

<style scoped>
.container {
  max-width: 860px;
  margin: 0 auto;
  padding: 30px 20px;
}

.header {
  text-align: center;
  margin-bottom: 20px;
}

.site-title {
  font-size: 2.2rem;
  font-weight: 300;
  margin-bottom: 12px;
  letter-spacing: 1px;
  color: var(--text-color);
}

.site-description {
  font-size: 1rem;
  color: var(--text-secondary);
  font-weight: 300;
  letter-spacing: 0.5px;
}



/* 響應式設計 */
@media (max-width: 768px) {
  .container {
    padding: 20px 16px;
  }
  
  .header {
    margin-bottom: 20px;
  }
  
  .site-title {
    font-size: 1.8rem;
  }
  
  .search-area {
    margin-bottom: 30px;
  }
}
</style>