import { defineConfig } from 'vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        wayfinder(),
        laravel({
            input: ['resources/js/app.ts', 'resources/css/app.css'],
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
    server: {
        port: 5173,
        strictPort: true,
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
            port: 5173,
        }
    },
});
