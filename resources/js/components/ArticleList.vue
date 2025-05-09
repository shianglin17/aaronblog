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
          <router-link :to="`/article/${article.id}`">
            {{ article.title }}
          </router-link>
        </h2>
        
        <div class="article-meta">
          <span class="article-author">
            <n-icon size="16" class="meta-icon"><PersonOutline /></n-icon>
            {{ article.author.name }}
          </span>
          <span class="article-category">
            <n-icon size="16" class="meta-icon"><FolderOutline /></n-icon>
            {{ article.category.name }}
          </span>
          <time :datetime="article.created_at">
            <n-icon size="16" class="meta-icon"><TimeOutline /></n-icon>
            {{ formatDate(article.created_at) }}
          </time>
        </div>
        
        <div class="article-tags" v-if="article.tags && article.tags.length > 0">
          <n-icon size="16" class="meta-icon tag-label"><PricetagOutline /></n-icon>
          <span 
            v-for="tag in article.tags" 
            :key="tag.id" 
            class="article-tag"
          >
            {{ tag.name }}
          </span>
        </div>
        
        <div class="article-content">
          {{ article.content.substring(0, 150) }}{{ article.content.length > 150 ? '...' : '' }}
        </div>
      </article>
    </div>
      
    <!-- 分頁 -->
    <div v-if="pagination && articles.length > 0" class="pagination">
      <n-pagination
        v-model:page="currentPage"
        :page-count="pagination.total_pages"
        :on-update:page="handlePageChange"
      />
      <div class="pagination-info">
        顯示 {{ (pagination.current_page - 1) * pagination.per_page + 1 }} - 
        {{ Math.min(pagination.current_page * pagination.per_page, pagination.total_items) }} 筆，
        共 {{ pagination.total_items }} 筆
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits, onMounted, computed } from 'vue';
import { 
  NPagination, 
  NIcon 
} from 'naive-ui';
import { 
  PersonOutline, 
  TimeOutline, 
  FolderOutline, 
  PricetagOutline 
} from '@vicons/ionicons5';
import type { Article } from '../types/article';
import type { PaginationMeta } from '../types/common';
import { useRouter } from 'vue-router';
import { withDefaults } from 'vue';

const props = defineProps<{
  articles: Article[];
  error?: string;
  pagination?: PaginationMeta;
}>();

const emit = defineEmits(['page-change', 'page-size-change']);
const router = useRouter();
const currentPage = ref(1);

// 初始化
onMounted(() => {
  if (props.pagination) {
    currentPage.value = props.pagination.current_page;
  }
});

// 處理分頁變更
const handlePageChange = (page: number) => {
  emit('page-change', page);
};

// 格式化日期
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('zh-TW', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};
</script>

<style scoped>
.article-list {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 16px;
}

.articles {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.article-item {
  border-bottom: 1px solid var(--border-color, #e0e0e0);
  padding-bottom: 30px;
}

.article-title {
  font-size: 1.6rem;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--text-color);
  line-height: 1.4;
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
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: center;
}

.meta-icon {
  margin-right: 4px;
  color: var(--text-secondary);
  display: inline-flex;
  transform: translateY(1px);
}

.article-tags {
  margin-bottom: 14px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.tag-label {
  color: var(--text-secondary);
}

.article-tag {
  background-color: var(--tag-bg, #f0f0f0);
  color: var(--text-secondary);
  font-size: 0.85rem;
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
  transition: background-color 0.2s, color 0.2s;
}

.article-tag:hover {
  background-color: var(--primary-color, #7d6e5d);
  color: white;
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
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.pagination-info {
  font-size: 0.9rem;
  color: var(--text-secondary);
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

  .article-meta {
    flex-direction: column;
    gap: 6px;
    align-items: flex-start;
  }

  .pagination {
    overflow-x: auto;
    padding: 10px 0;
  }
}
</style>