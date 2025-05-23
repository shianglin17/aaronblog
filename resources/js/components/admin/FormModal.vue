<template>
  <n-modal v-model:show="showModal" preset="card" :title="title" :style="{ width: width }">
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
import { ref, watch } from 'vue';

// 表單引用
const formRef = ref<any>(null);

// 表單提交中狀態
const submitting = ref(false);

// 模態窗顯示狀態（內部管理，與外部同步）
const showModal = ref(false);

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
</style> 