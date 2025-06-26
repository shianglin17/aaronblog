import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import naive from 'naive-ui'

// 引入樣式檔案
import '../css/app.css'

// 創建 Vue 應用
const app = createApp(App)

// 使用路由
app.use(router)

// 全域安裝 Naive UI
app.use(naive)

// 掛載應用
app.mount('#app') 