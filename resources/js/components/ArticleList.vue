<template>
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <article 
      v-for="article in articles"
      :key="article.id"
      class="border-b border-gray-200 pb-8 mb-8 last:border-b-0 last:mb-0"
    >
      <h2 class="text-2xl font-serif mb-4">
        {{ article.title }}
      </h2>
      <div class="text-sm text-gray-500 mb-4">
        {{ article.date }} · {{ article.authors }}
      </div>
      <p class="text-gray-700 line-clamp-3 mb-4">
        {{ article.content }}
        <a href="#" class="text-blue-500">
          ... →
        </a>
      </p>
      <hr class="border-gray-200 my-8">
    </article>
  </div>
</template>

<script setup lang="ts">
interface Article {
  id: number
  date: string
  authors: string
  title: string
  content: string
  tags: string[]
}
defineProps<{
  articles: Article[]  // 這裡定義 articles 是一個 Article 陣列
}>()
</script>

<style scoped>
</style>