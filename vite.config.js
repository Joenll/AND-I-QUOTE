import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
    ],
    server: {
        proxy: {
            '/api': {
                target: 'http://localhost:8000', // Laravel dev server
                changeOrigin: true,
                secure: false,
            },
        },
    },
});
