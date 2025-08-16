<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <n-message-provider>
            <div class="app-container">
                <component :is="currentLayout">
                    <router-view></router-view>
                </component>
            </div>
        </n-message-provider>
    </n-config-provider>
</template>

<script setup lang="ts">
import { defineOptions, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import FrontendLayout from './components/layouts/FrontendLayout.vue'
import AdminLayout from './components/admin/AdminLayout.vue'
import { getThemeConfig, getThemeTypeFromRoute } from './themes'

defineOptions({
    name: 'App'
})

const route = useRoute()

// 根據路由選擇布局
const currentLayout = computed(() => {
    if (route.path.startsWith('/admin')) {
        return AdminLayout
    }
    return FrontendLayout
})

// 根據路由動態選擇主題
const currentThemeType = computed(() => getThemeTypeFromRoute(route.path))
const currentThemeConfig = computed(() => getThemeConfig(currentThemeType.value))
const themeOverrides = computed(() => currentThemeConfig.value.themeOverrides)

// 動態注入 CSS 變數
const injectThemeCSS = (cssVariables: string) => {
    // 移除舊的主題樣式
    const existingStyle = document.getElementById('dynamic-theme-style')
    if (existingStyle) {
        existingStyle.remove()
    }
    
    // 注入新的主題樣式
    const style = document.createElement('style')
    style.id = 'dynamic-theme-style'
    style.textContent = cssVariables
    document.head.appendChild(style)
}

// 設置 HTML 根元素的 data-theme 屬性
const setThemeAttribute = (themeType: string) => {
    document.documentElement.setAttribute('data-theme', themeType)
}

// 監聽路由變化，更新主題
watch(currentThemeType, (newThemeType) => {
    const config = getThemeConfig(newThemeType)
    injectThemeCSS(config.cssVariables)
    setThemeAttribute(newThemeType)
}, { immediate: true })

// 組件掛載時初始化主題
onMounted(() => {
    const config = getThemeConfig(currentThemeType.value)
    injectThemeCSS(config.cssVariables)
    setThemeAttribute(currentThemeType.value)
})
</script>

<style>
/* 基礎樣式重置和全局設定 */
/* CSS 變數現在由主題動態注入，不再在這裡定義 */

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