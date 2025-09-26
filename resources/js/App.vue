<template>
    <n-config-provider :theme-overrides="adminThemeOverrides">
        <n-message-provider>
            <div class="app-container">
                <AdminLayout>
                    <router-view></router-view>
                </AdminLayout>
            </div>
        </n-message-provider>
    </n-config-provider>
</template>

<script setup lang="ts">
import { defineOptions } from 'vue'
import AdminLayout from './components/admin/AdminLayout.vue'
import { adminThemeOverrides } from './themes/admin'

defineOptions({
    name: 'App'
})
</script>

<style>
/* 後台專用基礎樣式 */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html, body {
    height: 100%;
    width: 100%;
}

.app-container {
    width: 100%;
    min-height: 100vh;
    padding: 0;
    overflow-x: hidden;
}
</style> 