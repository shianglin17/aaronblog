<template>
  <div class="frontend-layout">
    <main class="main-content">
      <slot></slot>
    </main>
    <Footer />
  </div>
</template>

<script setup lang="ts">
import Footer from '../Footer.vue';
</script>

<style scoped>
.frontend-layout {
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
</style>