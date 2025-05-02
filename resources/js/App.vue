<template>
    <div id="app">
        <router-view></router-view>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'App'
})
</script>

<style>
body {
    margin: 0;
    padding: 0;
    background-color: #faecda; /* 暖色系背景 */
    color: #2c3e50;
    line-height: 1.6;
}

#app {
    font-family: 'Times New Roman', serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style> 