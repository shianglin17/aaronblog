<template>
  <footer class="footer">
    <p>© {{ currentYear }} 個人部落格</p>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue';

// 計算當前年份
const currentYear = computed(() => new Date().getFullYear());
</script>

<style scoped>
.footer {
  margin-top: 80px;
  padding-top: 20px;
  text-align: center;
  color: var(--text-secondary);
  font-size: 0.85rem;
  border-top: 1px solid var(--border-color);
}

/* 響應式設計 */
@media (max-width: 768px) {
  .footer {
    margin-top: 60px;
  }
}
</style> 