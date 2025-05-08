import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import ArticleDetail from '../pages/ArticleDetail.vue'

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/article/:id',
        name: 'article-detail',
        component: ArticleDetail,
        props: true
    }
]

const router = createRouter({
    history: createWebHistory('/'),
    routes
})

export default router 