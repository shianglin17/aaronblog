<template>
  <div class="container">
    <header class="hero-section animate-fade-in">
      <div class="hero-content">
        <h1 class="site-title">Aaron 的部落格</h1>
        <p class="site-description">軟體開發紀錄、生活分享</p>
      </div>
    </header>

    <!-- 統一搜尋器 -->
    <div class="search-wrapper animate-fade-in-delay">
      <UnifiedSearchBar
        :categories="categories"
        :tags="tags"
        @update:filters="handleFilterChange"
      />
    </div>
  
    <!-- 文章列表 -->
    <div class="content-wrapper animate-fade-in-delay-2">
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
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 20px;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, var(--hero-bg-start), var(--hero-bg-end));
  border-radius: var(--hero-border-radius);
  padding: var(--hero-padding);
  margin-bottom: var(--hero-margin-bottom);
  box-shadow: var(--hero-shadow);
  position: relative;
  overflow: hidden;
}

.hero-content {
  text-align: center;
  position: relative;
  z-index: 2;
}

.site-title {
  font-size: var(--title-font-size);
  font-weight: var(--title-font-weight);
  margin-bottom: var(--title-margin-bottom);
  letter-spacing: var(--title-letter-spacing);
  color: var(--title-color);
  text-shadow: var(--title-text-shadow);
}

.site-description {
  font-size: var(--description-font-size);
  color: var(--description-color);
  font-weight: var(--description-font-weight);
  letter-spacing: var(--description-letter-spacing);
  max-width: var(--description-max-width);
  margin: 0 auto;
  line-height: var(--description-line-height);
}



/* 響應式設計 */
@media (max-width: 768px) {
  .container {
    padding: 20px 16px;
  }
  
  .hero-section {
    padding: 40px 20px;
    margin-bottom: 30px;
    border-radius: 12px;
  }
  
  .site-title {
    font-size: 2rem;
  }
  
  .site-description {
    font-size: 1rem;
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  .hero-section {
    padding: 32px 16px;
  }
  
  .site-title {
    font-size: 1.75rem;
  }
  
  .site-description {
    font-size: 0.95rem;
  }
}

/* 動畫效果 */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeInUp 0.6s ease-out;
}

.animate-fade-in-delay {
  animation: fadeInUp 0.6s ease-out 0.2s both;
}

.animate-fade-in-delay-2 {
  animation: fadeInUp 0.6s ease-out 0.4s both;
}

/* 為加載狀態添加脈衝效果 */
.n-spin-container {
  transition: var(--card-transition);
}

.search-wrapper {
  margin-bottom: 48px;
  transition: var(--card-transition);
}

.content-wrapper {
  transition: var(--card-transition);
}
</style>