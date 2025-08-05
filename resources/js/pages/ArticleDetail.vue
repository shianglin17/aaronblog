<template>
  <div class="container">
    <n-spin :show="loading" description="載入中...">
      <!-- 錯誤訊息 -->
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <!-- 返回按鈕 - 移至文章內容外部 -->
      <div v-else-if="article" class="navigation-back">
        <n-button quaternary type="primary" @click="goBack">
          <template #icon>
            <n-icon><ArrowBackOutline /></n-icon>
          </template>
          返回文章列表
        </n-button>
      </div>
      
      <!-- 文章內容 -->
      <div v-if="article" class="article-detail">
        <h1 class="article-title">{{ article.title }}</h1>
        
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
        
        <div class="article-content">
          <div class="markdown-wrapper">
            <div class="markdown-body" v-html="renderedContent"></div>
          </div>
        </div>
      </div>
      
      <!-- 空資料提示 -->
      <div v-else class="empty-message">
        找不到文章
      </div>
    </n-spin>
    
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { 
  ArrowBackOutline,
  PersonOutline, 
  TimeOutline, 
  FolderOutline, 
  PricetagOutline 
} from '@vicons/ionicons5';
import { articleApi } from '../api/index';
import type { Article } from '../types/article';
import { formatDate } from '../utils/date';
import { useMarkdown } from '../composables/useMarkdown';

// 定義接收的 props
const props = defineProps<{
  slug?: string;
  id?: number;
}>();

const router = useRouter();
const article = ref<Article | null>(null);
const loading = ref(true);
const error = ref('');
const { renderMarkdown } = useMarkdown();



// 渲染 markdown 內容
const renderedContent = computed(() => {
  if (!article.value?.content) return '';
  return renderMarkdown(article.value.content);
});

onMounted(async () => {
  try {
    // 從 props 獲取文章 ID
    const id = props.id;
    
    if (!id || isNaN(id) || id <= 0) {
      throw new Error('無效的文章 ID');
    }
    
    const response = await articleApi.getById(id);
    article.value = response.data;
  } catch (err) {
    error.value = '無法載入文章，請稍後再試';
    console.error('文章載入錯誤:', err);
  } finally {
    loading.value = false;
  }
});

// 返回上一頁
const goBack = () => {
  router.back();
};
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 32px;
  background: var(--background-color);
  min-height: 100vh;
}

.navigation-back {
  margin: 30px 0 20px;
}

.article-detail {
  background: var(--surface-secondary);
  padding: 40px;
  margin-top: 20px;
  border-radius: 16px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
}

.article-title {
  font-size: 2.2rem;
  font-weight: 700;
  margin-bottom: 24px;
  line-height: 1.3;
  color: var(--text-color);
}

.article-meta-wrapper {
  margin-bottom: 36px;
}

.article-meta {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-bottom: 16px;
  font-size: 0.9rem;
  color: var(--text-secondary, #666);
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
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
  margin-top: 10px;
}

.tag-label {
  color: var(--text-secondary);
}

.article-tag {
  background: var(--brand-light);
  color: var(--brand-primary);
  font-size: 0.8rem;
  padding: 6px 14px;
  border-radius: 20px;
  display: inline-block;
  transition: var(--transition-normal);
  font-weight: 600;
  border: 1px solid rgba(139, 69, 19, 0.2);
  cursor: pointer;
}

.article-tag:hover {
  background: var(--brand-gradient);
  color: white;
  border-color: transparent;
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}



.error-message {
  padding: 24px;
  color: var(--error-color);
  text-align: center;
  border: 1px solid var(--error-color);
  border-radius: 12px;
  background: var(--error-light);
  box-shadow: var(--shadow-sm);
  margin: 30px 0;
}

.empty-message {
  padding: 60px 24px;
  text-align: center;
  color: var(--text-tertiary);
  background: var(--surface-secondary);
  border-radius: 16px;
  border: 2px dashed var(--border-color);
  font-size: 1.1rem;
  margin: 30px 0;
}

.article-status {
  background-color: var(--tag-bg, #f0f0f0);
  font-size: 0.85rem;
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
}

.article-status.published {
  background: var(--success-light);
  color: var(--success-color);
  border: 1px solid var(--success-color);
}

.article-status.draft {
  background: var(--warning-light);
  color: var(--warning-color);
  border: 1px solid var(--warning-color);
}

/* 文章內容區域 */
.article-content {
  margin-top: 32px;
}

.markdown-wrapper {
  background: var(--surface-elevated);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 32px;
  box-shadow: var(--shadow-sm);
}

.markdown-body {
  line-height: 1.75;
  color: var(--text-color);
  font-size: 1.05rem;
}

/* Markdown 內容樣式優化 */
.markdown-body h1,
.markdown-body h2,
.markdown-body h3,
.markdown-body h4,
.markdown-body h5,
.markdown-body h6 {
  color: var(--text-color);
  margin: 1.5em 0 0.75em 0;
  font-weight: 600;
}

.markdown-body h1:first-child,
.markdown-body h2:first-child,
.markdown-body h3:first-child {
  margin-top: 0;
}

.markdown-body p {
  margin: 1em 0;
  line-height: 1.8;
}

.markdown-body code {
  background: var(--surface-tertiary);
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'SF Mono', Monaco, Consolas, monospace;
  font-size: 0.9em;
  color: var(--brand-primary);
}

.markdown-body pre {
  background: var(--surface-tertiary);
  padding: 16px;
  border-radius: 8px;
  overflow-x: auto;
  margin: 1.5em 0;
  border: 1px solid var(--border-color);
}

.markdown-body pre code {
  background: none;
  padding: 0;
  color: var(--text-color);
}

.markdown-body blockquote {
  border-left: 4px solid var(--brand-primary);
  padding: 0 16px;
  margin: 1.5em 0;
  background: var(--brand-light);
  border-radius: 0 8px 8px 0;
}

.markdown-body ul,
.markdown-body ol {
  margin: 1em 0;
  padding-left: 1.5em;
}

.markdown-body li {
  margin: 0.5em 0;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .container {
    padding: 0 20px;
  }
  
  .navigation-back {
    margin: 20px 0 10px;
  }
  
  .container {
    padding: 20px;
  }
  
  .article-detail {
    padding: 24px;
  }
  
  .article-title {
    font-size: 1.8rem;
    margin-bottom: 20px;
  }
  
  .article-meta {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }
  
  .article-content {
    font-size: 1rem;
  }
  
  .markdown-wrapper {
    padding: 0 4px;
  }
}
</style> 