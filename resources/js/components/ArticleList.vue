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
        @click="navigateToArticle(article)"
        tabindex="0"
        @keydown.enter="navigateToArticle(article)"
      >
        <h2 class="article-title">
          {{ article.title }}
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
import { useRouter } from 'vue-router';
import { formatDate } from '../utils/date';

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

// 導航到文章詳情頁
const navigateToArticle = (article: Article) => {
  router.push({
    path: `/article/${article.slug}`,
    query: { id: article.id?.toString() || '' }
  });
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
  background: var(--card-background);
  border-radius: var(--card-border-radius);
  padding: var(--card-padding);
  box-shadow: var(--card-shadow);
  border: var(--card-border);
  transition: var(--card-transition);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.article-title {
  font-size: 1.6rem;
  font-weight: 700;
  margin-bottom: 16px;
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
  margin-right: 4px;
  color: var(--text-secondary);
  display: inline-flex;
  transform: translateY(1px);
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
  background-color: var(--tag-background);
  color: var(--tag-color);
  font-size: 0.85rem;
  padding: var(--tag-padding);
  border-radius: var(--tag-border-radius);
  display: inline-block;
  transition: var(--card-transition);
  font-weight: 500;
}

.article-tag:hover {
  background-color: var(--tag-hover-background);
  color: var(--tag-hover-color);
  transform: translateY(-1px);
}

.article-description {
  line-height: 1.75;
  margin-bottom: 16px;
  color: var(--text-color);
  font-size: 1rem;
  position: relative;
  padding-right: 24px;
}

.read-more-link {
  display: inline-flex;
  align-items: center;
  color: var(--primary-color, #7d6e5d);
  margin-left: 6px;
  position: absolute;
  right: 0;
  bottom: 0;
}

.read-more-icon {
  transition: transform 0.2s;
}

.read-more-link:hover .read-more-icon {
  transform: translateX(3px);
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
  padding: 20px;
  color: #e74c3c;
  text-align: center;
  border: 1px solid #f9e4e4;
  border-radius: 4px;
  background-color: #fdf2f2;
}

.empty-message {
  padding: 50px 0;
  text-align: center;
  color: var(--text-secondary);
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
}

@media (max-width: 480px) {
  .article-item {
    padding: 16px;
  }
  
  .article-title {
    font-size: 1.3rem;
  }
}

/* 文章卡片互動效果 */
.article-item:hover, .article-item:focus {
  box-shadow: var(--card-shadow-hover);
  transform: translateY(-2px);
  outline: none;
}

.article-item:focus {
  border-color: var(--primary-color);
}

.article-item:active {
  transform: translateY(0);
}
</style>