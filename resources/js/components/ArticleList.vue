<template>
  <div class="article-list">
    <!-- 錯誤訊息 -->
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
    
    <!-- 空資料提示 -->
    <div v-else-if="articles.length === 0" class="empty-message">
      尚未發布任何文章
    </div>
    
    <!-- 文章列表 -->
    <div v-else class="articles">
      <article 
        v-for="article in articles"
        :key="article.id"
        class="article-item"
      >
        <h2 class="article-title">
          {{ article.title }}
        </h2>
        
        <div class="article-meta">
          <time :datetime="article.created_at">{{ formatDate(article.created_at) }}</time>
        </div>
        
        <div class="article-content">
          {{ article.content.substring(0, 150) }}{{ article.content.length > 150 ? '...' : '' }}
        </div>
      </article>
    </div>
      
    <!-- 分頁 -->
    <div v-if="pagination && pagination.last_page > 1" class="pagination">
      <n-pagination 
        v-model:page="currentPage"
        :page-count="pagination.last_page" 
        :page-sizes="[10, 20, 50]"
        :page-size="pagination.per_page"
        :item-count="pagination.total"
        size="medium"
        @update:page="handlePageChange"
      />
      </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { NPagination } from 'naive-ui'
import type { Article } from '../types/article'
import type { PaginationMeta } from '../types/common'
import { formatDate } from '../utils/date'

// Props
const props = defineProps<{
  articles: Article[]
  pagination?: PaginationMeta
  error?: string
}>()

// Emits
const emit = defineEmits(['page-change'])

// 當前頁面
const currentPage = computed({
  get: () => props.pagination?.current_page || 1,
  set: (val) => emit('page-change', val)
})

// 處理頁面變更
function handlePageChange(page: number) {
  emit('page-change', page)
}
</script>

<style scoped>
.article-list {
  width: 100%;
}

.error-message {
  padding: 20px;
  color: #e74c3c;
  text-align: center;
  border: 1px solid #f9e4e4;
  border-radius: 4px;
  background-color: #fdf2f2;
}

.empty-message {
  padding: 40px 0;
  text-align: center;
  color: var(--text-secondary);
}

.articles {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.article-item {
  padding-bottom: 30px;
  border-bottom: 1px solid var(--border-color);
}

.article-item:last-child {
  border-bottom: none;
}

.article-title {
  margin: 0 0 12px;
  font-size: 1.6rem;
  font-weight: 400;
  letter-spacing: 0.5px;
}

.article-title a {
  color: var(--text-color);
  text-decoration: none;
  transition: color 0.2s;
}

.article-title a:hover {
  color: var(--primary-color, #7d6e5d);
}

.article-meta {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin-bottom: 14px;
}

.article-content {
  line-height: 1.75;
  margin-bottom: 20px;
  color: var(--text-color);
  font-size: 1rem;
}

.article-actions {
  margin-top: 16px;
}

.read-more {
  display: inline-block;
  font-size: 0.95rem;
  color: var(--primary-color, #7d6e5d);
  text-decoration: none;
  border-bottom: 1px solid transparent;
  transition: border-color 0.2s;
}

.read-more:hover {
  border-color: currentColor;
}

.pagination {
  margin-top: 50px;
  display: flex;
  justify-content: center;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .articles {
    gap: 30px;
  }

  .article-item {
    padding-bottom: 20px;
  }

  .article-title {
    font-size: 1.4rem;
  }

  .pagination {
    margin-top: 30px;
    overflow-x: auto;
    padding: 10px 0;
  }
}
</style>