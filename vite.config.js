import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/article.css',  // 新增文章專用樣式
                'resources/js/app.ts'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    build: {
        // 調整分塊大小警告閾值（單位：KB）
        chunkSizeWarningLimit: 1000, // 從預設的 500KB 提高到 1000KB
        rollupOptions: {
            output: {
                // 手動分塊：將 markdown 相關庫分離
                manualChunks: {
                    'markdown': ['markdown-it', 'highlight.js', 'github-markdown-css'],
                    'vue-vendor': ['vue', 'vue-router'],
                    'ui-vendor': ['naive-ui']
                }
            }
        }
    }
});
