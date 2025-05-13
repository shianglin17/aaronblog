import { defineAsyncComponent } from 'vue'
import { createRouter, createWebHistory, RouteLocationNormalized } from 'vue-router'
import Home from '../pages/Home.vue'
import ArticleDetail from '../pages/ArticleDetail.vue'
import Login from '../pages/Login.vue'
import { isLoggedIn } from '../api/auth'

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/article/:slug',
        name: 'article-detail',
        component: ArticleDetail,
        props: (route: RouteLocationNormalized) => ({ 
            slug: route.params.slug,
            id: Number(route.query.id) 
        })
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            requiresGuest: true
        }
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('../pages/admin/Dashboard.vue'),
        meta: {
            requiresAuth: true
        }
    }
]

const router = createRouter({
    history: createWebHistory('/'),
    routes
})

// 全局路由守衛
router.beforeEach((to, from, next) => {
    // 檢查是否需要登入
    if (to.meta.requiresAuth && !isLoggedIn()) {
        // 需要登入但未登入，重定向到登入頁面
        next({ name: 'login' });
    } 
    // 檢查是否需要訪客狀態（如登入頁面）
    else if (to.meta.requiresGuest && isLoggedIn()) {
        // 需要訪客狀態但已登入，重定向到管理頁面
        next({ name: 'admin' });
    } 
    else {
        // 其他情況正常放行
        next();
    }
});

export default router 