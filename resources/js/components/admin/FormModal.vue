<template>
  <n-modal v-model:show="showModal" preset="card" :title="title" :style="modalStyle">
    <n-form
      v-if="showModal"
      ref="formRef"
      :model="props.modelValue"
      :rules="rules"
      label-placement="left"
      label-width="auto"
      require-mark-placement="right-hanging"
    >
      <slot></slot>
      
      <div class="form-actions">
        <n-button @click="closeModal">取消</n-button>
        <n-button type="primary" :loading="submitting" @click="handleSubmit">{{ submitText }}</n-button>
      </div>
    </n-form>
  </n-modal>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';

// 表單引用
const formRef = ref<any>(null);

// 表單提交中狀態
const submitting = ref(false);

// 模態窗顯示狀態（內部管理，與外部同步）
const showModal = ref(false);

// 響應式模態窗樣式
const modalStyle = computed(() => {
  // 使用 CSS 媒體查詢處理響應式，這裡僅處理基本樣式
  return {
    width: props.width,
    // 在小螢幕上使用 CSS 變數覆蓋寬度
    '--modal-width-xs': '95vw',
    '--modal-width-sm': '90vw',
    '--modal-width-md': '80vw'
  };
});

const props = defineProps({
  // 模態窗標題
  title: {
    type: String,
    required: true
  },
  // 模態窗寬度
  width: {
    type: String,
    default: '600px'
  },
  // 表單數據
  modelValue: {
    type: Object,
    required: true
  },
  // 表單驗證規則
  rules: {
    type: Object,
    default: () => ({})
  },
  // 是否顯示模態窗
  show: {
    type: Boolean,
    default: false
  },
  // 提交按鈕文字
  submitText: {
    type: String,
    default: '提交'
  }
});

const emit = defineEmits(['update:show', 'update:model-value', 'submit', 'cancel']);

// 監聽外部模態窗顯示狀態
watch(() => props.show, (newVal) => {
  showModal.value = newVal;
});

// 監聽內部模態窗顯示狀態，同步到外部
watch(showModal, (newVal, oldVal) => {
  if (newVal !== props.show) {
    emit('update:show', newVal);
  }
  
  if (!newVal) {
    setTimeout(() => {
      formRef.value?.restoreValidation();
    }, 0);
  }
});

// 提交表單
async function handleSubmit() {
  if (!formRef.value) return;
  
  try {
    submitting.value = true;
    await formRef.value.validate();
    emit('submit', props.modelValue);
  } catch (error) {
    console.error('表單驗證失敗', error);
  } finally {
    submitting.value = false;
  }
}

// 關閉模態窗
function closeModal() {
  showModal.value = false;
  emit('cancel');
}
</script>

<style scoped>
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}

/* 響應式表單設計 */

/* 桌機：正常顯示 */
@media (min-width: 992px) {
  .form-actions {
    justify-content: flex-end;
  }
}

/* 平板：適度調整 */
@media (min-width: 768px) and (max-width: 991px) {
  .form-actions {
    gap: 10px;
    margin-top: 16px;
  }
  
  .form-actions :deep(.n-button) {
    padding: 6px 16px;
  }
}

/* 手機：垂直排列按鈕 */
@media (max-width: 767px) {
  .form-actions {
    flex-direction: column-reverse;
    gap: 8px;
    margin-top: 16px;
  }
  
  .form-actions :deep(.n-button) {
    width: 100%;
    justify-content: center;
    padding: 10px 16px;
    font-size: 0.875rem;
  }
}

/* 極小螢幕：進一步優化 */
@media (max-width: 479px) {
  .form-actions {
    margin-top: 12px;
    gap: 6px;
  }
  
  .form-actions :deep(.n-button) {
    padding: 8px 16px;
    font-size: 0.8rem;
  }
}

/* 模態窗響應式寬度 */
@media (max-width: 479px) {
  :deep(.n-modal) {
    width: var(--modal-width-xs) !important;
    max-width: var(--modal-width-xs) !important;
    margin: 8px !important;
  }
}

@media (min-width: 480px) and (max-width: 767px) {
  :deep(.n-modal) {
    width: var(--modal-width-sm) !important;
    max-width: var(--modal-width-sm) !important;
    margin: 16px !important;
  }
}

@media (min-width: 768px) and (max-width: 991px) {
  :deep(.n-modal) {
    width: var(--modal-width-md) !important;
    max-width: 600px !important;
  }
}

/* 表單項目響應式 */
@media (max-width: 767px) {
  :deep(.n-form) {
    --n-label-width: 80px !important;
  }
  
  :deep(.n-form-item-label) {
    font-size: 0.875rem;
  }
  
  :deep(.n-input) {
    font-size: 0.875rem;
  }
  
  :deep(.n-select) {
    font-size: 0.875rem;
  }
  
  :deep(.n-form-item) {
    margin-bottom: 16px;
  }
}

@media (max-width: 479px) {
  :deep(.n-form) {
    --n-label-width: 70px !important;
  }
  
  :deep(.n-form-item-label) {
    font-size: 0.8rem;
  }
  
  :deep(.n-input) {
    font-size: 0.8rem;
  }
  
  :deep(.n-select) {
    font-size: 0.8rem;
  }
  
  :deep(.n-form-item) {
    margin-bottom: 12px;
  }
}
</style> 