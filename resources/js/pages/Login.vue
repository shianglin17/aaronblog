<template>
  <div class="login-container">
    <div class="login-form-wrapper">
      <div class="login-header">
        <h1 class="login-title">管理員登入</h1>
        <p class="login-subtitle">請輸入您的帳號密碼進行登入</p>
      </div>
      
      <n-alert 
        v-if="errorMessage" 
        type="error" 
        :title="errorMessage" 
        closable 
        class="login-alert"
      />
      
      <n-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-placement="top"
        size="large"
        @submit.prevent="handleLogin"
      >
        <n-form-item path="email" label="電子郵件">
          <n-input
            v-model:value="form.email"
            placeholder="請輸入您的電子郵件"
            clearable
            :style="inputStyle"
          >
            <template #prefix>
              <n-icon><mail-outline /></n-icon>
            </template>
          </n-input>
        </n-form-item>
        
        <n-form-item path="password" label="密碼">
          <n-input
            v-model:value="form.password"
            type="password"
            placeholder="請輸入您的密碼"
            clearable
            show-password-on="click"
            :style="inputStyle"
          >
            <template #prefix>
              <n-icon><lock-closed-outline /></n-icon>
            </template>
          </n-input>
        </n-form-item>
        
        <div class="login-options">
          <n-checkbox v-model:checked="form.remember">
            記住我
          </n-checkbox>
        </div>
        
        <n-button 
          type="primary" 
          block 
          :loading="isLoading" 
          attr-type="submit"
          size="large"
          class="login-button"
        >
          {{ isLoading ? '登入中...' : '登入' }}
        </n-button>
      </n-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { 
  NForm, 
  NFormItem, 
  NInput, 
  NCheckbox, 
  NButton, 
  NAlert,
  NIcon,
  FormRules, 
  FormInst 
} from 'naive-ui';
import { MailOutline, LockClosedOutline } from '@vicons/ionicons5';
import { login } from '../api/auth';
import type { LoginParams } from '../types/auth';

const router = useRouter();
const formRef = ref<FormInst | null>(null);
const isLoading = ref(false);
const errorMessage = ref('');

// 統一輸入框樣式
const inputStyle = {
  width: '100%'
};

const form = reactive<LoginParams>({
  email: '',
  password: '',
  remember: false
});

const rules: FormRules = {
  email: [
    { required: true, message: '請輸入電子郵件', trigger: 'blur' },
    { type: 'email', message: '請輸入有效的電子郵件格式', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '請輸入密碼', trigger: 'blur' },
    { min: 6, message: '密碼長度至少為6個字元', trigger: 'blur' }
  ]
};

async function handleLogin() {
  // 清除先前的錯誤
  errorMessage.value = '';
  
  try {
    await formRef.value?.validate();
    isLoading.value = true;
    const response = await login(form);
    
    if (response.status === 'success') {
      // 成功登入，導向管理頁面
      router.push('/admin');
    } else {
      // 處理一般錯誤（不應該走到這裡，因為非成功回應會拋出錯誤）
      errorMessage.value = response.message || '登入失敗，請稍後再試';
    }
  } catch (error: any) {
    // 處理表單驗證錯誤
    if (!error.response) {
      console.error('表單驗證失敗:', error);
      return;
    }
    
    // 處理API錯誤
    if (error.response) {
      const { status, data } = error.response;
      
      if (status === 401) {
        // 認證失敗
        errorMessage.value = data.message || '電子郵件或密碼不正確';
      } else if (status === 422) {
        // 驗證錯誤
        errorMessage.value = data.message || '請檢查您輸入的資料';
        
        // 如果有具體驗證錯誤，顯示第一條錯誤
        if (data.meta && data.meta.errors) {
          const errors = data.meta.errors;
          // 取得第一個錯誤訊息
          for (const field in errors) {
            if (errors[field] && Array.isArray(errors[field]) && errors[field].length > 0) {
              errorMessage.value = errors[field][0];
              break;
            }
          }
        }
      } else {
        // 其他錯誤
        errorMessage.value = data.message || '登入失敗，請稍後再試';
      }
    } else {
      // 網路錯誤或其他非預期錯誤
      errorMessage.value = '無法連接到伺服器，請檢查您的網路連接並稍後再試';
      console.error('登入錯誤:', error);
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: var(--body-bg, #f9f9f9);
}

.login-form-wrapper {
  width: 100%;
  max-width: 420px;
  padding: 2.5rem;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.login-header {
  margin-bottom: 1.8rem;
  text-align: center;
}

.login-title {
  margin-bottom: 0.5rem;
  font-size: 1.8rem;
  font-weight: 500;
  color: var(--text-color, #333);
}

.login-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary, #666);
  font-weight: 400;
}

.login-alert {
  margin-bottom: 1.5rem;
}

.login-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.login-button {
  margin-top: 0.5rem;
  font-weight: 500;
  height: 44px;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .login-form-wrapper {
    max-width: 90%;
    padding: 2rem 1.5rem;
    margin: 0 20px;
  }
  
  .login-title {
    font-size: 1.6rem;
  }
}
</style> 