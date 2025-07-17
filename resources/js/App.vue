<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <n-message-provider>
            <div class="app-container">
                <main class="main-content">
                    <router-view></router-view>
                </main>
                <Footer />
            </div>
        </n-message-provider>
    </n-config-provider>
</template>

<script setup lang="ts">
import { defineOptions } from 'vue'
import Footer from './components/Footer.vue'

defineOptions({
    name: 'App'
})

const themeOverrides = {
    common: {
        primaryColor: '#7d6e5d',
        primaryColorHover: '#8f8072',
        primaryColorPressed: '#6d5e4d',
        borderRadius: '4px',
        fontWeight: '400',
        textColor1: '#333333',
        textColor2: '#4e4e4e'
    },
    Card: {
        borderRadius: '8px'
    },
    Button: {
        textColor: '#4e4e4e',
        borderRadius: '4px'
    }
}
</script>

<style>
/* （:root 內）：這些在全站任何地方都可以通過 var() 函數訪問 */
:root {
    /* 基礎色彩 */
    --background-color: #f8f5f2;
    --text-color: #333333;
    --text-secondary: #666666;
    --border-color: #e0ddd7;
    --primary-color: #7d6e5d;
    --primary-hover: #8f8072;
    
    /* Hero 區域變數 */
    --hero-bg-start: #fdfcfb;
    --hero-bg-end: #f5f3f0;
    --hero-border-radius: 16px;
    --hero-padding: 64px 40px;
    --hero-margin-bottom: 48px;
    --hero-shadow: 0 8px 32px rgba(125, 110, 93, 0.1);
    
    /* 標題樣式變數 */
    --title-font-size: 2.5rem;
    --title-font-weight: 500;
    --title-margin-bottom: 16px;
    --title-letter-spacing: 0.5px;
    --title-color: var(--text-color);
    --title-text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    
    /* 描述文字變數 */
    --description-font-size: 1.125rem;
    --description-color: var(--text-secondary);
    --description-font-weight: 400;
    --description-letter-spacing: 0.3px;
    --description-max-width: 600px;
    --description-line-height: 1.7;
    
    /* 文章卡片變數 */
    --card-background: #ffffff;
    --card-border-radius: 12px;
    --card-padding: 24px;
    --card-gap: 24px;
    --card-shadow: 0 2px 16px rgba(125, 110, 93, 0.08);
    --card-shadow-hover: 0 8px 32px rgba(125, 110, 93, 0.15);
    --card-border: 1px solid rgba(125, 110, 93, 0.1);
    --card-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* 標籤變數 */
    --tag-background: rgba(125, 110, 93, 0.08);
    --tag-color: var(--primary-color);
    --tag-hover-background: var(--primary-color);
    --tag-hover-color: #ffffff;
    --tag-border-radius: 16px;
    --tag-padding: 4px 12px;
    
    /* 搜尋器變數 */
    --search-background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
    --search-border: 2px solid #e8e5e0;
    --search-border-radius: 16px;
    --search-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.08);
    --search-shadow-focus: 0 8px 32px rgba(125, 110, 93, 0.15), 0 2px 8px rgba(125, 110, 93, 0.1), 0 0 0 4px rgba(125, 110, 93, 0.05);
    --search-input-height: 48px;
    --search-padding: 6px 20px;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html, body {
    height: 100%;
    width: 100%;
}

body {
    background-color: var(--background-color);
    color: var(--text-color);
    font-family: 'Noto Serif TC', serif;
    font-size: 16px;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.app-container {
    width: 100%;
    min-height: 100vh;
    padding: 0;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
}

.main-content {
    flex: 1;
}

a {
    color: #7d6e5d;
    text-decoration: none;
    transition: color 0.2s ease;
}

a:hover {
    color: #8f8072;
}
</style> 