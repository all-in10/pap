import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/service-worker.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'service-worker') {
                        return 'js/sw.js';
                    }
                    return 'js/[name]-[hash].js';
                },
            },
        },
    },
});
