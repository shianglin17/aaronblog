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
          <!-- 文章狀態只在後台顯示 -->
          <span v-if="isAdmin" class="article-status" :class="article.status">
            {{ article.status === 'published' ? '已發佈' : '草稿' }}
          </span>
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
          {{ article.content }}
        </div>
      </div>
      
      <!-- 空資料提示 -->
      <div v-else class="empty-message">
        找不到文章
      </div>
    </n-spin>
    
    <!-- 使用 Footer 元件 -->
    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { NSpin, NIcon, NButton } from 'naive-ui';
import { 
  ArrowBackOutline,
  PersonOutline, 
  TimeOutline, 
  FolderOutline, 
  PricetagOutline 
} from '@vicons/ionicons5';
import Footer from '../components/Footer.vue';
import { getArticleById } from '../api/article';
import type { Article } from '../types/article';

const route = useRoute();
const router = useRouter();
const article = ref<Article | null>(null);
const loading = ref(true);
const error = ref('');
const isAdmin = computed(() => {
  // 從本地儲存檢查是否為管理員
  // 這裡可以根據實際的驗證方式調整
  return localStorage.getItem('is_admin') === 'true';
});

onMounted(async () => {
  try {
    const id = route.params.id as string;
    const response = await getArticleById(parseInt(id));
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
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 16px;
}

.article-back {
  margin: 20px 0;
}

.article-detail {
  padding: 20px 0;
}

.article-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 16px;
  line-height: 1.3;
  color: var(--text-color);
}

.article-meta {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 16px;
  font-size: 0.9rem;
  color: var(--text-secondary);
  align-items: center;
}

.meta-icon {
  margin-right: 4px;
  color: var(--text-secondary);
  display: inline-flex;
  transform: translateY(1px);
}

.article-tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 20px;
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
  line-height: 1.8;
  font-size: 1.05rem;
  color: var(--text-color);
  white-space: pre-line;
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

.article-status {
  background-color: var(--tag-bg, #f0f0f0);
  font-size: 0.85rem;
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
}

.article-status.published {
  background-color: #e6f7e6;
  color: #2e7d32;
}

.article-status.draft {
  background-color: #fff8e1;
  color: #ff8f00;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .article-title {
    font-size: 1.8rem;
  }
  
  .article-meta {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }
  
  .article-content {
    font-size: 1rem;
  }
}
</style> 