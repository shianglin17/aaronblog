<template>
  <div class="container">
    <n-spin :show="loading" description="載入中...">
      <!-- 錯誤訊息 -->
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <!-- 文章內容 -->
      <div v-else-if="article" class="article-detail">
        <div class="article-back">
          <n-button quaternary type="primary" @click="goBack">
            <template #icon>
              <n-icon><ArrowBackOutline /></n-icon>
            </template>
            返回文章列表
          </n-button>
        </div>
        
        <h1 class="article-title">{{ article.title }}</h1>
        
        <div class="article-meta">
          <span class="article-author">作者: {{ article.user_name }}</span>
          <span class="article-category">分類: {{ article.category_name }}</span>
          <time :datetime="article.created_at">發佈時間: {{ formatDate(article.created_at) }}</time>
        </div>
        
        <div class="article-tags" v-if="article.tags && article.tags.length > 0">
          <span class="tag-label">標籤:</span>
          <span 
            v-for="tag in article.tags" 
            :key="tag.id" 
            class="article-tag"
          >
            {{ tag.name }}
          </span>
        </div>
        
        <div class="article-content">
          {{ article.content }}
        </div>
      </div>
      
      <!-- 空資料提示 -->
      <div v-else class="empty-message">
        找不到文章
      </div>
    </n-spin>
    
    <!-- 頁腳 -->
    <footer class="footer">
      <p>© {{ new Date().getFullYear() }} 個人部落格</p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { NSpin, NIcon, NButton } from 'naive-ui';
import { ArrowBackOutline } from '@vicons/ionicons5';
import { getArticleById } from '../api/article';
import type { Article } from '../types/article';
import { formatDate } from '../utils/date';

// 路由
const route = useRoute();
const router = useRouter();

// 狀態管理
const article = ref<Article | null>(null);
const loading = ref(true);
const error = ref('');

// 獲取文章詳情
async function fetchArticleDetail() {
  loading.value = true;
  error.value = '';
  
  try {
    // 從路由參數獲取文章 ID
    const articleId = parseInt(route.params.id as string);
    
    if (isNaN(articleId)) {
      throw new Error('無效的文章 ID');
    }
    
    const response = await getArticleById(articleId);
    article.value = response.data;
  } catch (err: any) {
    if (err.response && err.response.status === 404) {
      error.value = '文章不存在';
    } else {
      error.value = '獲取文章詳情失敗，請稍後再試';
    }
    console.error(err);
  } finally {
    loading.value = false;
  }
}

// 返回文章列表
function goBack() {
  router.push('/');
}

// 掛載時獲取文章詳情
onMounted(() => {
  fetchArticleDetail();
});
</script>

<style scoped>
.container {
  max-width: 860px;
  margin: 0 auto;
  padding: 30px 20px;
}

.article-back {
  margin-bottom: 24px;
}

.article-detail {
  padding-bottom: 40px;
}

.article-title {
  font-size: 2rem;
  font-weight: 400;
  margin-bottom: 16px;
  letter-spacing: 0.5px;
  line-height: 1.4;
}

.article-meta {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin-bottom: 14px;
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.article-tags {
  margin-bottom: 24px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.tag-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.article-tag {
  background-color: var(--tag-bg, #f0f0f0);
  color: var(--text-secondary);
  font-size: 0.85rem;
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
}

.article-content {
  line-height: 1.8;
  font-size: 1.1rem;
  color: var(--text-color);
  white-space: pre-wrap;
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

.footer {
  margin-top: 80px;
  padding-top: 20px;
  text-align: center;
  color: var(--text-secondary);
  font-size: 0.85rem;
  border-top: 1px solid var(--border-color);
}

/* 響應式設計 */
@media (max-width: 768px) {
  .container {
    padding: 20px 16px;
  }
  
  .article-title {
    font-size: 1.6rem;
  }
  
  .article-meta {
    flex-direction: column;
    gap: 6px;
  }
  
  .article-content {
    font-size: 1rem;
  }
}
</style> 