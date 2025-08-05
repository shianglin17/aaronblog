<template>
  <div class="home-layout">
    <!-- 緊湊式導航區域 -->
    <CompactNavigation 
      :categories="categories"
      :tags="tags"
      @search="handleSearch"
      @clear-search="clearSearch"
      @category-filter="filterByCategory"
      @tag-filter="filterByTags"
      @clear-category-filter="clearCategoryFilter"
      @clear-tag-filter="clearTagFilter"
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
        :categories="categories"
        :tags="tags"
        @page-change="changePage"
        @page-size-change="changePageSize"
        @clear-all-filters="clearAllFilters"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import CompactNavigation from '../components/layout/CompactNavigation.vue';
import ProfileSidebar from '../components/profile/ProfileSidebar.vue';
import ArticlesSection from '../components/articles/ArticlesSection.vue';
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

// 篩選和導航狀態
const categories = ref<Category[]>([]);
const tags = ref<Tag[]>([]);

const currentParams = ref<ArticleListParams>({
  ...DEFAULT_PAGINATION_PARAMS,
  status: 'published'
});

// 計算屬性
const totalArticles = computed(() => pagination.value?.total_items || 0);
const totalCategories = computed(() => categories.value.length);
const totalTags = computed(() => tags.value.length);

// 獲取文章列表
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

// 搜尋處理
const handleSearch = (query: string) => {
  currentParams.value.search = query || undefined;
  currentParams.value.page = 1;
  fetchArticles();
};

const clearSearch = () => {
  currentParams.value.search = undefined;
  currentParams.value.page = 1;
  fetchArticles();
};

// 分類和標籤篩選
const filterByCategory = (categorySlug: string) => {
  currentParams.value.category = categorySlug;
  currentParams.value.page = 1;
  fetchArticles();
};

const filterByTags = (tagSlugs: string[]) => {
  currentParams.value.tags = tagSlugs.length > 0 ? tagSlugs : undefined;
  currentParams.value.page = 1;
  fetchArticles();
};

const clearCategoryFilter = () => {
  currentParams.value.category = undefined;
  currentParams.value.page = 1;
  fetchArticles();
};

const clearTagFilter = () => {
  currentParams.value.tags = undefined;
  currentParams.value.page = 1;
  fetchArticles();
};

const clearAllFilters = () => {
  currentParams.value.search = undefined;
  currentParams.value.category = undefined;
  currentParams.value.tags = undefined;
  currentParams.value.page = 1;
  fetchArticles();
};

// 獲取分類和標籤數據
async function fetchFilters() {
  try {
    const [categoriesResponse, tagsResponse] = await Promise.all([
      articleApi.getAllCategories(),
      tagApi.getList()
    ]);
    
    categories.value = categoriesResponse.data;
    tags.value = tagsResponse.data;
  } catch (err) {
    console.error('獲取篩選數據失敗:', err);
  }
}

// 掛載時獲取文章
onMounted(() => {
  fetchArticles();
  fetchFilters();
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