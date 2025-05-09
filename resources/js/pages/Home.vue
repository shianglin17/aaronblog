<template>
  <div class="container">
    <header class="header">
      <h1 class="site-title">Aaron 的部落格</h1>
      <p class="site-description">軟體開發紀錄、生活分享</p>
    </header>

    <!-- 搜尋區域 -->
    <div class="search-area">
      <n-input-group>
        <n-input 
          v-model:value="searchQuery"
          placeholder="搜尋文章..." 
          @keydown.enter="searchArticles"
          clearable
        />
        <n-button ghost @click="searchArticles">
          <template #icon>
            <n-icon><SearchOutline /></n-icon>
          </template>
        </n-button>
      </n-input-group>
    </div>
  
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

    <!-- 使用 Footer 元件 -->
    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { SearchOutline } from '@vicons/ionicons5';
import { NInput, NInputGroup, NButton, NSpin, NIcon } from 'naive-ui';
import ArticleList from '../components/ArticleList.vue';
import Footer from '../components/Footer.vue';
import { getArticleList } from '../api/article';
import type { Article, ArticleListParams } from '../types/article';
import type { PaginationMeta } from '../types/common';
import { DEFAULT_PAGINATION_PARAMS } from '../constants/pagination';

// 狀態管理
const articles = ref<Article[]>([]);
const loading = ref(true);
const error = ref('');
const pagination = ref<PaginationMeta | undefined>(undefined);
const searchQuery = ref('');
const currentParams = ref<ArticleListParams>({
  ...DEFAULT_PAGINATION_PARAMS
});

// 獲取文章列表
async function fetchArticles() {
  loading.value = true;
  error.value = '';
  
  try {
    const response = await getArticleList(currentParams.value);
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

// 搜尋文章
function searchArticles() {
  currentParams.value.search = searchQuery.value;
  currentParams.value.page = 1; // 重置頁碼
  fetchArticles();
}

// 掛載時獲取文章
onMounted(() => {
  fetchArticles();
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
  margin-bottom: 60px;
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

.search-area {
  max-width: 500px;
  margin: 0 auto 40px;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .container {
    padding: 20px 16px;
  }
  
  .header {
    margin-bottom: 40px;
  }
  
  .site-title {
    font-size: 1.8rem;
  }
  
  .search-area {
    margin-bottom: 30px;
  }
}
</style>