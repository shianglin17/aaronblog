<template>
    <div class="welcome-page">
        <h1>歡迎來到我的部落格</h1>
        <p>一個簡約、優雅的閱讀空間</p>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'Welcome',
    setup() {
        return {}
    }
})
</script>

<style scoped>
.welcome-page {
    padding: 2rem;
    text-align: center;
    font-family: 'Times New Roman', serif; /* 報紙風格字體 */
}

h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

p {
    font-size: 1.2rem;
    color: #34495e;
    line-height: 1.6;
}
</style> 