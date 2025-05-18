<template>
  <n-modal v-model:show="showModal" preset="card" :title="title" :style="{ width: width }">
    <n-form
      ref="formRef"
      :model="formData"
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
import { NModal, NForm, NButton } from 'naive-ui';

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

// 處理表單數據，用於雙向綁定
const formData = ref(props.modelValue);

// 監聽外部表單數據變化
watch(() => props.modelValue, (newVal) => {
  formData.value = { ...newVal };
}, { deep: true });

// 監聽內部表單數據變化，同步到外部
watch(formData, (newVal) => {
  emit('update:model-value', newVal);
}, { deep: true });

// 監聽外部模態窗顯示狀態
watch(() => props.show, (newVal) => {
  showModal.value = newVal;
});

// 監聽內部模態窗顯示狀態，同步到外部
watch(showModal, (newVal) => {
  emit('update:show', newVal);
  
  // 當模態窗關閉時，重置表單
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
    emit('submit', formData.value);
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

// 重置表單
function resetForm() {
  if (formRef.value) {
    formRef.value.restoreValidation();
  }
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