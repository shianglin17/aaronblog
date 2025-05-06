import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// 導入 Naive UI 全部組件
import {
  create,
  NButton,
  NCard,
  NConfigProvider, 
  NLoadingBarProvider, 
  NDialogProvider, 
  NNotificationProvider, 
  NMessageProvider,
  NInput,
  NInputGroup,
  NIcon,
  NSpin,
  NPagination,
  NText,
  NTime,
  NEllipsis,
  NEmpty,
  NAlert
} from 'naive-ui'

// 創建 Vue 應用
const app = createApp(App)

// 使用路由
app.use(router)

// 註冊 Naive UI 組件
const naive = create({
  components: [
    NButton,
    NCard,
    NConfigProvider, 
    NLoadingBarProvider, 
    NDialogProvider, 
    NNotificationProvider, 
    NMessageProvider,
    NInput,
    NInputGroup,
    NIcon,
    NSpin,
    NPagination,
    NText,
    NTime,
    NEllipsis,
    NEmpty,
    NAlert
  ]
})
app.use(naive)

// 掛載應用
app.mount('#app') 