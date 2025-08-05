<template>
  <transition name="mobile-menu">
    <div v-if="show" class="mobile-menu-overlay" @click="closeMobileMenu">
      <div class="mobile-menu-content" @click.stop>
        <div class="mobile-menu-header">
          <h3>選單</h3>
          <button @click="closeMobileMenu" class="close-btn">
            <n-icon size="24"><CloseOutline /></n-icon>
          </button>
        </div>
        
        <div class="mobile-menu-section">
          <h4>文章分類</h4>
          <div class="mobile-categories">
            <button 
              v-for="category in categories" 
              :key="category.id"
              @click="handleCategoryFilter(category.slug)"
              class="mobile-category-item"
            >
              {{ category.name }}
              <span class="item-count">{{ category.articles_count || 0 }}</span>
            </button>
            <button @click="handleClearCategoryFilter" class="mobile-category-item clear">
              全部分類
            </button>
          </div>
        </div>
        
        <div class="mobile-menu-section">
          <h4>熱門標籤</h4>
          <div class="mobile-tags">
            <button 
              v-for="tag in tags.slice(0, 16)" 
              :key="tag.id"
              @click="handleTagFilter(tag.slug)"
              class="mobile-tag-item"
            >
              {{ tag.name }}
            </button>
            <button @click="handleClearTagFilter" class="mobile-tag-item clear">
              全部標籤
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { CloseOutline } from '@vicons/ionicons5';
import type { Category } from '../../types/category';
import type { Tag } from '../../types/tag';

// Props
defineProps<{
  show: boolean;
  categories: Category[];
  tags: Tag[];
}>();

// Events
const emit = defineEmits<{
  'update:show': [show: boolean];
  'category-filter': [slug: string];
  'tag-filter': [slug: string];
  'clear-category-filter': [];
  'clear-tag-filter': [];
}>();

// 事件處理
const closeMobileMenu = () => {
  emit('update:show', false);
  document.body.style.overflow = '';
};

const handleCategoryFilter = (slug: string) => {
  emit('category-filter', slug);
  closeMobileMenu();
};

const handleTagFilter = (slug: string) => {
  emit('tag-filter', slug);
  closeMobileMenu();
};

const handleClearCategoryFilter = () => {
  emit('clear-category-filter');
  closeMobileMenu();
};

const handleClearTagFilter = () => {
  emit('clear-tag-filter');
  closeMobileMenu();
};
</script>

<style scoped>
/* 手機版全螢幕選單 */
.mobile-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 20px;
  overflow-y: auto;
}

.mobile-menu-content {
  background: var(--surface-elevated);
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  max-height: calc(100vh - 40px);
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
  margin-top: 60px;
}

.mobile-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid var(--border-color);
  background: var(--brand-gradient);
  color: white;
  border-radius: 16px 16px 0 0;
}

.mobile-menu-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  transition: var(--transition-fast);
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.mobile-menu-section {
  padding: 20px 24px;
  border-bottom: 1px solid var(--border-color);
}

.mobile-menu-section:last-child {
  border-bottom: none;
}

.mobile-menu-section h4 {
  margin: 0 0 16px 0;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-color);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.mobile-categories {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.mobile-category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: var(--surface-secondary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  color: var(--text-color);
  cursor: pointer;
  transition: var(--transition-normal);
  font-size: 0.9rem;
  text-align: left;
}

.mobile-category-item:hover {
  background: var(--brand-light);
  color: var(--brand-primary);
  border-color: var(--brand-primary);
}

.mobile-category-item.clear {
  background: var(--brand-light);
  color: var(--brand-primary);
  font-weight: 600;
}

.mobile-tags {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  gap: 8px;
}

.mobile-tag-item {
  padding: 8px 12px;
  background: var(--brand-light);
  border: 1px solid rgba(139, 69, 19, 0.2);
  border-radius: 16px;
  color: var(--brand-primary);
  cursor: pointer;
  transition: var(--transition-normal);
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
}

.mobile-tag-item:hover {
  background: var(--brand-gradient);
  color: white;
  border-color: transparent;
}

.mobile-tag-item.clear {
  background: var(--surface-secondary);
  color: var(--text-secondary);
  grid-column: 1 / -1;
}

.item-count {
  font-size: 0.75rem;
  color: var(--text-secondary);
  background: var(--surface-color);
  padding: 2px 6px;
  border-radius: 4px;
}

/* 手機選單動畫 */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: opacity var(--transition-normal);
}

.mobile-menu-enter-active .mobile-menu-content,
.mobile-menu-leave-active .mobile-menu-content {
  transition: transform var(--transition-normal);
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
}

.mobile-menu-enter-from .mobile-menu-content,
.mobile-menu-leave-to .mobile-menu-content {
  transform: translateY(-20px) scale(0.95);
}

/* 響應式調整 */
@media (max-width: 480px) {
  .mobile-menu-overlay {
    padding: 12px;
  }
  
  .mobile-menu-content {
    margin-top: 40px;
  }
  
  .mobile-menu-section {
    padding: 16px 20px;
  }
  
  .mobile-tags {
    grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
    gap: 6px;
  }
  
  .mobile-tag-item {
    padding: 6px 10px;
    font-size: 0.75rem;
  }
}
</style>