<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <n-message-provider>
            <div class="app-container">
                <router-view></router-view>
            </div>
        </n-message-provider>
    </n-config-provider>
</template>

<script setup lang="ts">
import { defineOptions } from 'vue'
import { NConfigProvider } from 'naive-ui'

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
    --background-color: #f8f5f2;
    --text-color: #333333;
    --text-secondary: #666666;
    --border-color: #e0ddd7;
    --primary-color: #7d6e5d;
    --primary-hover: #8f8072;
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