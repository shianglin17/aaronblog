import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// 創建 Vue 應用
const app = createApp(App)

// 使用路由
app.use(router)

// 掛載應用
app.mount('#app') 