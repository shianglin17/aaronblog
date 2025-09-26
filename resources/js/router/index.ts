import { createRouter, createWebHistory } from 'vue-router'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            requiresGuest: true
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            requiresGuest: true
        }
    },
    {
        path: '/admin',
        redirect: '/admin/articles'
    },
    {
        path: '/admin/articles',
        name: 'admin-articles',
        component: () => import('../pages/admin/ArticleManagement.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/admin/tags',
        name: 'admin-tags',
        component: () => import('../pages/admin/TagManagement.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/admin/categories',
        name: 'admin-categories',
        component: () => import('../pages/admin/CategoryManagement.vue'),
        meta: {
            requiresAuth: true
        }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// 全局路由守衛
router.beforeEach(async (to, _from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth) {
        // 需要認證的頁面
        const isAuthenticated = await authStore.checkAuth();
        if (isAuthenticated) {
            next();
        } else {
            next({ name: 'login', replace: true });
        }
    } else if (to.meta.requiresGuest) {
        // 訪客專用頁面（如登入頁）
        const isAuthenticated = await authStore.checkAuth();
        if (isAuthenticated) {
            next({ name: 'admin-articles', replace: true });
        } else {
            next();
        }
    } else {
        // 公開頁面
        next();
    }
});

export default router 
