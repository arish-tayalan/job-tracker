import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // dashboard.js and board.js are their own entries so each page only downloads what it needs.
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/board.js',
            ],
            refresh: true,
        }),
    ],
});
