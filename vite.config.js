import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // dashboard.js is its own entry so Chart.js is only downloaded on the dashboard page.
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js'],
            refresh: true,
        }),
    ],
});
