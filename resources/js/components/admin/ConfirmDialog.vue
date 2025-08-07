<template>
  <n-modal :show="show" @update:show="$emit('update:show', $event)" preset="card" style="width: 480px; max-width: 90vw;" :mask-closable="false">
    <template #header>
      <div class="confirm-header">
        <n-icon size="24" :color="iconColor" class="confirm-icon">
          <component :is="icon" />
        </n-icon>
        <span class="confirm-title">{{ title }}</span>
      </div>
    </template>
    
    <div class="confirm-content">
      <p class="confirm-message">{{ message }}</p>
      
      <!-- 項目資訊區域 -->
      <div v-if="itemInfo" class="item-info-card">
        <div class="item-header">
          <span class="item-type">{{ itemType }}</span>
          <span class="item-name">{{ itemInfo.name }}</span>
        </div>
        
        <div v-if="itemInfo.slug" class="item-detail">
          <span class="detail-label">Slug:</span>
          <span class="detail-value">{{ itemInfo.slug }}</span>
        </div>
        
        <div v-if="'description' in itemInfo && itemInfo.description" class="item-detail">
          <span class="detail-label">描述:</span>
          <span class="detail-value">{{ itemInfo.description }}</span>
        </div>
        
        <!-- 警告區域 -->
        <div v-if="showWarning" class="warning-section">
          <n-alert 
            :type="warningAlertType" 
            :show-icon="true"
            class="warning-alert"
          >
            <template #icon>
              <n-icon><component :is="warningIcon" /></n-icon>
            </template>
            <div class="warning-content">
              <div class="warning-title">{{ warningTitle }}</div>
              <div class="warning-details">
                <div v-if="itemInfo.articles_count > 0" class="warning-item">
                  📄 包含 <strong>{{ itemInfo.articles_count }}</strong> 篇關聯文章
                </div>
                <div class="warning-consequence">
                  {{ warningConsequence }}
                </div>
              </div>
            </div>
          </n-alert>
        </div>
      </div>
    </div>
    
    <template #action>
      <div class="confirm-actions">
        <n-button @click="handleCancel" size="medium">
          取消
        </n-button>
        <n-button 
          :type="confirmType" 
          @click="handleConfirm" 
          size="medium"
          :loading="loading"
          :disabled="isConfirmDisabled"
          ghost
        >
          <template #icon>
            <n-icon><component :is="confirmIcon" /></n-icon>
          </template>
          {{ confirmText }}
        </n-button>
      </div>
    </template>
  </n-modal>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { TrashOutline, WarningOutline, AlertCircleOutline, BanOutline } from '@vicons/ionicons5';
import type { Category } from '../../types/category';
import type { Tag } from '../../types/tag';

// Props
interface Props {
  show: boolean;
  title?: string;
  message?: string;
  itemType?: string;
  itemInfo?: Category | Tag | null;
  confirmText?: string;
  confirmType?: 'error' | 'warning' | 'info';
  loading?: boolean;
  deleteMode?: 'strict' | 'loose'; // 新增：刪除模式
}

const props = withDefaults(defineProps<Props>(), {
  title: '確認刪除',
  message: '確定要執行此操作嗎？',
  itemType: '項目',
  confirmText: '確認刪除',
  confirmType: 'error',
  loading: false,
  deleteMode: 'loose'
});

// Events
const emit = defineEmits<{
  'update:show': [show: boolean];
  confirm: [];
  cancel: [];
}>();

// 計算屬性
const icon = computed(() => {
  switch (props.confirmType) {
    case 'warning':
      return WarningOutline;
    case 'error':
      return TrashOutline;
    default:
      return AlertCircleOutline;
  }
});

const confirmIcon = computed(() => {
  return props.confirmType === 'error' ? TrashOutline : AlertCircleOutline;
});

const iconColor = computed(() => {
  switch (props.confirmType) {
    case 'error':
      return 'var(--error-color, #ef4444)';
    case 'warning':
      return 'var(--warning-color, #f59e0b)';
    default:
      return 'var(--info-color, #3b82f6)';
  }
});

const showWarning = computed(() => {
  return props.itemInfo && props.itemInfo.articles_count && props.itemInfo.articles_count > 0;
});

// 根據刪除模式決定警告樣式和內容
const warningAlertType = computed(() => {
  if (props.deleteMode === 'strict') {
    return 'error'; // 嚴格模式：錯誤提示（不允許刪除）
  }
  return 'warning'; // 寬鬆模式：警告提示（允許但需注意）
});

const warningIcon = computed(() => {
  if (props.deleteMode === 'strict') {
    return BanOutline; // 禁止圖標
  }
  return WarningOutline; // 警告圖標
});

const warningTitle = computed(() => {
  if (props.deleteMode === 'strict') {
    return '無法刪除！此操作被阻止';
  }
  return '注意！此操作會影響其他內容';
});

const warningConsequence = computed(() => {
  if (props.deleteMode === 'strict') {
    return `無法刪除此${props.itemType}，因為仍有文章正在使用。請先將相關文章轉移到其他${props.itemType}。`;
  }
  return `刪除後，相關文章會失去此${props.itemType}的關聯，但文章本身不會被刪除。`;
});

const isConfirmDisabled = computed(() => {
  // 嚴格模式下，如果有關聯文章則禁用確認按鈕
  if (props.deleteMode === 'strict' && showWarning.value) {
    return true;
  }
  return props.loading;
});

// 事件處理
const handleConfirm = () => {
  emit('confirm');
};

const handleCancel = () => {
  emit('cancel');
  emit('update:show', false);
};
</script>

<style scoped>
.confirm-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.confirm-icon {
  flex-shrink: 0;
}

.confirm-title {
  font-weight: 600;
  color: var(--text-color);
}

.confirm-content {
  padding: 8px 0;
}

.confirm-message {
  margin: 0 0 16px 0;
  color: var(--text-secondary);
  font-size: 14px;
  line-height: 1.5;
}

.item-info-card {
  background: var(--surface-secondary, #f8fafc);
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 8px;
  padding: 16px;
  margin-top: 12px;
}

.item-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.item-type {
  background: var(--brand-light, #f0f9ff);
  color: var(--brand-primary, #0369a1);
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.item-name {
  font-weight: 600;
  color: var(--text-color);
}

.item-detail {
  display: flex;
  gap: 8px;
  margin-bottom: 6px;
  font-size: 13px;
}

.detail-label {
  color: var(--text-tertiary);
  min-width: 40px;
  font-weight: 500;
}

.detail-value {
  color: var(--text-secondary);
  word-break: break-all;
}

.warning-section {
  margin-top: 16px;
}

.warning-alert {
  border-radius: 6px;
}

.warning-content {
  font-size: 13px;
}

.warning-title {
  font-weight: 600;
  color: var(--warning-color, #f59e0b);
  margin-bottom: 6px;
}

.warning-details {
  color: var(--text-secondary);
}

.warning-item {
  margin-bottom: 4px;
}

.warning-item strong {
  color: var(--warning-color, #f59e0b);
}

.warning-consequence {
  font-size: 12px;
  color: var(--text-tertiary);
  font-style: italic;
}

.confirm-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

/* 深色主題支援 */
@media (prefers-color-scheme: dark) {
  .item-info-card {
    background: var(--surface-secondary, #1f2937);
    border-color: var(--border-color, #374151);
  }
  
  .item-type {
    background: var(--brand-dark, #1e3a8a);
    color: var(--brand-light, #dbeafe);
  }
}

/* 響應式設計 */
@media (max-width: 640px) {
  .confirm-actions {
    flex-direction: column-reverse;
  }
  
  .item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
  
  .item-detail {
    flex-direction: column;
    gap: 2px;
  }
}
</style>