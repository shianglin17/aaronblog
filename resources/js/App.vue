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
        primaryColor: '#8b4513',
        primaryColorHover: '#d2691e',
        primaryColorPressed: '#6b3410',
        borderRadius: '8px',
        fontWeight: '500',
        textColor1: '#2d1810',
        textColor2: '#5a4037',
        borderColor: '#efebe6'
    },
    Card: {
        borderRadius: '12px',
        color: '#ffffff'
    },
    Button: {
        textColor: '#5a4037',
        borderRadius: '8px',
        colorHover: '#fdf8f3'
    },
    Input: {
        borderRadius: '8px',
        borderHover: '#d2691e'
    }
}
</script>

<style>
/* （:root 內）：這些在全站任何地方都可以通過 var() 函數訪問 */
:root {
    /* 溫暖棕色系設計系統 */
    --background-color: #fefdfb; /* 溫暖背景色 */
    --text-color: #2d1810; /* 深棕色文字 */
    --text-secondary: #5a4037; /* 次要文字 */
    --text-tertiary: #8d6e63; /* 三級文字 */
    --border-color: #efebe6; /* 溫和邊框 */
    
    /* 漸層主色調系統 */
    --primary-50: #f7fafc;
    --primary-100: #edf2f7;
    --primary-200: #e2e8f0;
    --primary-300: #cbd5e0;
    --primary-400: #a0aec0;
    --primary-500: #2d3748; /* 主色 */
    --primary-600: #4a5568;
    --primary-700: #1a202c;
    --primary-800: #171923;
    --primary-900: #0d1117;
    
    /* 品牌色彩系統 - 溫暖棕色系 */
    --brand-primary: #8b4513; /* 深棕色 */
    --brand-secondary: #d2691e; /* 巧克力色 */
    --brand-tertiary: #daa520; /* 金棕色 */
    --brand-gradient: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 60%, var(--brand-tertiary) 100%);
    --brand-light: #fdf8f3;
    
    /* 語義色彩 */
    --success-color: #38a169; /* 成功綠 */
    --success-light: #f0fff4;
    --warning-color: #ed8936; /* 警告橙 */
    --warning-light: #fffaf0;
    --error-color: #e53e3e; /* 錯誤紅 */
    --error-light: #fed7d7;
    --info-color: #3182ce; /* 資訊藍 */
    --info-light: #ebf8ff;
    
    /* 表面層次系統 - 溫暖色調 */
    --surface-color: #ffffff; /* 純白卡片 */
    --surface-elevated: #ffffff; /* 懸浮卡片 */
    --surface-hover: #fdf8f3; /* hover 背景 */
    --surface-active: #f9f2ec; /* active 背景 */
    --surface-secondary: #faf7f2; /* 次要背景 */
    --surface-tertiary: #f3ede5; /* 三級背景 */
    
    /* 陰影系統 - 溫暖色調 */
    --shadow-xs: 0 1px 2px 0 rgba(139, 69, 19, 0.08);
    --shadow-sm: 0 1px 3px 0 rgba(139, 69, 19, 0.12), 0 1px 2px 0 rgba(139, 69, 19, 0.08);
    --shadow-md: 0 4px 6px -1px rgba(139, 69, 19, 0.12), 0 2px 4px -1px rgba(139, 69, 19, 0.08);
    --shadow-lg: 0 10px 15px -3px rgba(139, 69, 19, 0.12), 0 4px 6px -2px rgba(139, 69, 19, 0.06);
    --shadow-xl: 0 20px 25px -5px rgba(139, 69, 19, 0.12), 0 10px 10px -5px rgba(139, 69, 19, 0.06);
    
    /* 互動狀態 */
    --hover-opacity: 0.8;
    --active-scale: 0.95;
    --transition-fast: 0.15s ease-out;
    --transition-normal: 0.2s ease-out;
    --transition-slow: 0.3s ease-out;
    
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