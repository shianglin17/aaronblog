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
        @click="goToArticle(article.slug)"
        tabindex="0"
        @keydown.enter="goToArticle(article.slug)"
      >
        <h2 class="article-title">
          <a class="article-title-link" :href="`/article/${article.slug}`" @click.stop>
            {{ article.title }}
          </a>
        </h2>
        
        <!-- 文章元數據區塊 -->
        <div class="article-meta-wrapper">
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
        </div>
        
        <!-- 文章描述 -->
        <div v-if="article.description" class="article-description">
          {{ article.description }}
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
import { ref, onMounted } from 'vue';
import { 
  PersonOutline, 
  TimeOutline, 
  FolderOutline, 
  PricetagOutline,
} from '@vicons/ionicons5';
import type { Article } from '../types/article';
import type { PaginationMeta } from '../types/common';
// 使用 SSR，改為標準連結導向
import { formatDate } from '../utils/date';

const props = defineProps<{
  articles: Article[];
  error?: string;
  pagination?: PaginationMeta;
}>();

const emit = defineEmits(['page-change', 'page-size-change']);
const currentPage = ref(1);

// 初始化
onMounted(() => {
  if (props.pagination) {
    currentPage.value = props.pagination.current_page;
  }
});

// 導向至 SSR 文章詳情頁
const goToArticle = (slug: string) => {
  window.location.assign(`/article/${slug}`);
};

// 處理分頁變更
const handlePageChange = (page: number) => {
  emit('page-change', page);
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
  gap: var(--card-gap);
}

.article-item {
  background: var(--surface-elevated);
  border-radius: 16px;
  padding: 24px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: var(--transition-normal);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.article-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 16px;
  color: var(--text-color);
  line-height: 1.4;
  transition: var(--transition-normal);
}

.article-title a {
  color: var(--text-color);
  text-decoration: none;
  transition: color 0.2s;
}

.article-title-link {
  display: inline-block;
}

.article-title:hover {
  background: var(--brand-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.article-meta-wrapper {
  margin-bottom: 20px;
}

.article-meta {
  font-size: 0.9rem;
  color: var(--text-secondary, #666);
  margin-bottom: 10px;
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: center;
}

.meta-icon {
  margin-right: 6px;
  color: var(--brand-primary);
  display: inline-flex;
  transform: translateY(1px);
  opacity: 0.8;
}

.article-tags {
  margin-bottom: 0;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.tag-label {
  color: var(--text-secondary);
}

.article-tag {
  background: var(--brand-light);
  color: var(--brand-primary);
  font-size: 0.8rem;
  padding: 4px 12px;
  border-radius: 16px;
  display: inline-block;
  transition: var(--transition-normal);
  font-weight: 600;
  border: 1px solid rgba(102, 126, 234, 0.2);
}

.article-tag:hover {
  background: var(--brand-gradient);
  color: white;
  border-color: transparent;
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.article-description {
  line-height: 1.6;
  margin-bottom: 16px;
  color: var(--text-secondary);
  font-size: 0.95rem;
  position: relative;
  padding: 16px 20px;
  background: rgba(139, 69, 19, 0.03);
  border-left: 4px solid var(--brand-primary);
  border-radius: 0 8px 8px 0;
  font-style: italic;
  quotes: """ """;
}

.article-description::before {
  content: open-quote;
  font-size: 1.5em;
  color: var(--brand-primary);
  opacity: 0.6;
  line-height: 0;
  position: absolute;
  top: 12px;
  left: 8px;
  font-family: "Times New Roman", serif;
}

.article-description::after {
  content: close-quote;
  font-size: 1.5em;
  color: var(--brand-primary);
  opacity: 0.6;
  line-height: 0;
  position: absolute;
  bottom: 6px;
  right: 8px;
  font-family: "Times New Roman", serif;
}


.pagination {
  margin-top: 40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.pagination-info {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.error-message {
  padding: 24px;
  color: var(--error-color);
  text-align: center;
  border: 1px solid var(--error-color);
  border-radius: 12px;
  background: var(--error-light);
  box-shadow: var(--shadow-sm);
}

.empty-message {
  padding: 60px 24px;
  text-align: center;
  color: var(--text-tertiary);
  background: var(--surface-secondary);
  border-radius: 16px;
  border: 2px dashed var(--border-color);
  font-size: 1.1rem;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .articles {
    gap: 16px;
  }

  .article-item {
    padding: 20px;
    border-radius: 8px;
  }
  
  .article-title {
    font-size: 1.4rem;
    margin-bottom: 14px;
  }
  
  .article-meta {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }
  
  .article-description {
    padding: 12px 16px;
    font-size: 0.9rem;
    margin-bottom: 12px;
  }
  
  .article-description::before {
    top: 8px;
    left: 6px;
    font-size: 1.3em;
  }
  
  .article-description::after {
    bottom: 4px;
    right: 6px;
    font-size: 1.3em;
  }
}

@media (max-width: 480px) {
  .article-item {
    padding: 16px;
  }
  
  .article-title {
    font-size: 1.3rem;
  }
  
  .article-description {
    padding: 10px 14px;
    font-size: 0.85rem;
    border-left-width: 3px;
  }
  
  .article-description::before {
    top: 6px;
    left: 4px;
    font-size: 1.2em;
  }
  
  .article-description::after {
    bottom: 2px;
    right: 4px;
    font-size: 1.2em;
  }
}

/* 文章卡片互動效果 - 現代化 */
.article-item:hover, .article-item:focus {
  box-shadow: var(--shadow-lg);
  transform: translateY(-4px);
  outline: none;
  border-color: var(--brand-primary);
}

.article-item:focus {
  box-shadow: var(--shadow-lg), 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.article-item:active {
  transform: translateY(-2px) scale(var(--active-scale));
}
</style>
