<template>
  <div class="register-container">
    <div class="register-form-wrapper">
      <div class="register-header">
        <h1 class="register-title">用戶註冊</h1>
        <p class="register-subtitle">請輸入您的資料進行註冊</p>
      </div>
      
      <n-alert 
        v-if="errorMessage" 
        type="error" 
        :title="errorMessage" 
        closable 
        class="register-alert"
        @close="errorMessage = ''"
      />
      
      <n-alert 
        v-if="successMessage" 
        type="success" 
        :title="successMessage" 
        closable 
        class="register-alert"
        @close="successMessage = ''"
      />
      
      <n-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-placement="top"
        size="large"
        @submit.prevent="handleRegister"
      >
        <n-form-item path="name" label="姓名">
          <n-input
            v-model:value="form.name"
            placeholder="請輸入您的姓名"
            clearable
            :style="inputStyle"
          >
            <template #prefix>
              <n-icon><person-outline /></n-icon>
            </template>
          </n-input>
        </n-form-item>
        
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
            placeholder="請輸入密碼 (至少6個字元)"
            clearable
            show-password-on="click"
            :style="inputStyle"
          >
            <template #prefix>
              <n-icon><lock-closed-outline /></n-icon>
            </template>
          </n-input>
        </n-form-item>
        
        <n-form-item path="password_confirmation" label="確認密碼">
          <n-input
            v-model:value="form.password_confirmation"
            type="password"
            placeholder="請再次輸入密碼"
            clearable
            show-password-on="click"
            :style="inputStyle"
          >
            <template #prefix>
              <n-icon><lock-closed-outline /></n-icon>
            </template>
          </n-input>
        </n-form-item>
        
        <n-form-item path="invite_code" label="邀請碼">
          <n-input
            v-model:value="form.invite_code"
            placeholder="請輸入邀請碼"
            clearable
            :style="inputStyle"
          >
            <template #prefix>
              <n-icon><key-outline /></n-icon>
            </template>
          </n-input>
        </n-form-item>
        
        <n-button 
          type="primary" 
          block 
          :loading="authStore.isLoading" 
          attr-type="submit"
          size="large"
          class="register-button"
        >
          {{ authStore.isLoading ? '註冊中...' : '註冊' }}
        </n-button>
        
        <div class="register-footer">
          <p>已有帳號？
            <router-link to="/login" class="login-link">立即登入</router-link>
          </p>
        </div>
      </n-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { FormRules, FormInst } from 'naive-ui';
import { 
  MailOutline, 
  LockClosedOutline, 
  PersonOutline,
  KeyOutline
} from '@vicons/ionicons5';
import { useAuthStore } from '../stores/auth';
import type { RegisterParams } from '../types/auth';

const router = useRouter();
const authStore = useAuthStore();
const formRef = ref<FormInst | null>(null);
const errorMessage = ref('');
const successMessage = ref('');

// 統一輸入框樣式
const inputStyle = {
  width: '100%'
};

const form = reactive<RegisterParams>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  invite_code: ''
});

// 自定義確認密碼驗證規則
const validatePasswordConfirm = (_rule: any, value: string) => {
  if (!value) {
    return Promise.reject('請確認密碼');
  }
  if (value !== form.password) {
    return Promise.reject('兩次輸入的密碼不一致');
  }
  return Promise.resolve();
};

const rules: FormRules = {
  name: [
    { required: true, message: '請輸入姓名', trigger: 'blur' },
    { min: 2, message: '姓名長度至少為2個字元', trigger: 'blur' },
    { max: 50, message: '姓名長度不能超過50個字元', trigger: 'blur' }
  ],
  email: [
    { required: true, message: '請輸入電子郵件', trigger: 'blur' },
    { type: 'email', message: '請輸入有效的電子郵件格式', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '請輸入密碼', trigger: 'blur' },
    { min: 6, message: '密碼長度至少為6個字元', trigger: 'blur' }
  ],
  password_confirmation: [
    { required: true, validator: validatePasswordConfirm, trigger: ['input', 'blur'] }
  ],
  invite_code: [
    { required: true, message: '請輸入邀請碼', trigger: 'blur' }
  ]
};

async function handleRegister() {
  // 清除先前的訊息
  errorMessage.value = '';
  successMessage.value = '';
  
  try {
    await formRef.value?.validate();
    
    const result = await authStore.register(form);
    
    if (result.success) {
      successMessage.value = '註冊成功！正在導向管理頁面...';
      
      // 延遲一下讓用戶看到成功訊息，然後導向管理頁面
      setTimeout(() => {
        router.push('/admin');
      }, 1500);
    } else {
      // 顯示錯誤訊息
      errorMessage.value = result.message || '註冊失敗，請稍後再試';
    }
  } catch (error: any) {
    // 處理表單驗證錯誤
    if (!error.response) {
      console.error('表單驗證失敗:', error);
      return;
    }
    
    // 處理 API 錯誤
    if (error.response?.data?.errors) {
      // Laravel 驗證錯誤
      const errors = error.response.data.errors;
      const firstError = Object.values(errors)[0];
      errorMessage.value = Array.isArray(firstError) ? firstError[0] : String(firstError);
    } else {
      // 其他錯誤
      errorMessage.value = error.response?.data?.message || '註冊失敗，請檢查您的網路連接並稍後再試';
    }
    
    console.error('註冊錯誤:', error);
  }
}
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: var(--body-bg, #f9f9f9);
}

.register-form-wrapper {
  width: 100%;
  max-width: 450px;
  padding: 2.5rem;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.register-header {
  margin-bottom: 1.8rem;
  text-align: center;
}

.register-title {
  margin-bottom: 0.5rem;
  font-size: 1.8rem;
  font-weight: 500;
  color: var(--text-color, #333);
}

.register-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary, #666);
  font-weight: 400;
}

.register-alert {
  margin-bottom: 1.5rem;
}

.register-button {
  margin-top: 0.5rem;
  font-weight: 500;
  height: 44px;
}

.register-footer {
  margin-top: 1.5rem;
  text-align: center;
}

.register-footer p {
  font-size: 0.9rem;
  color: var(--text-secondary, #666);
  margin: 0;
}

.login-link {
  color: var(--primary-color, #18a058);
  text-decoration: none;
  font-weight: 500;
}

.login-link:hover {
  text-decoration: underline;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .register-form-wrapper {
    max-width: 90%;
    padding: 2rem 1.5rem;
    margin: 0 20px;
  }
  
  .register-title {
    font-size: 1.6rem;
  }
}
</style>