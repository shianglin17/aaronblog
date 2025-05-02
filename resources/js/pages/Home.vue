<template>
    <main class="home-page">
        <div class="max-w-4xl mx-auto">
            <h1>Welcome to my blog</h1>
            <p>A simple and elegant reading space</p>

            <hr class="border-gray-200 my-8">
            <ArticleList :articles="articlesListData"/>
        </div>
    </main>
</template>

<script setup lang="ts">
import articleListMock from '../mock/article/article-list.json'
import ArticleList from '../components/ArticleList.vue'
const articlesListData = articleListMock.data.items

</script>

<style scoped>
.home-page {
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