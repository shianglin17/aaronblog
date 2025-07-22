import { createRouter, createWebHistory, RouteLocationNormalized } from 'vue-router'
import Home from '../pages/Home.vue'
import ArticleDetail from '../pages/ArticleDetail.vue'
import About from '../pages/About.vue'
import SiteInfo from '../pages/SiteInfo.vue'
import Login from '../pages/Login.vue'
import { authApi } from '../api/index'

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
        path: '/about',
        name: 'about',
        component: About
    },
    {
        path: '/site-info',
        name: 'site-info',
        component: SiteInfo
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
    history: createWebHistory('/'),
    routes
})

// 全局路由守衛
router.beforeEach(async (to, from, next) => {
    // 檢查認證狀態
    const checkAuth = async (): Promise<boolean> => {
        try {
            const response = await authApi.checkAuth();
            return response.status === 'success';
        } catch {
            return false;
        }
    };

    if (to.meta.requiresAuth) {
        // 需要認證的頁面
        const isAuthenticated = await checkAuth();
        if (isAuthenticated) {
            next();
        } else {
            next({ name: 'login', replace: true });
        }
    } else if (to.meta.requiresGuest) {
        // 訪客專用頁面（如登入頁）
        const isAuthenticated = await checkAuth();
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