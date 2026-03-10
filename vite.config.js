import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/stepper.js',
                'resources/js/modal.js'
            ],
            refresh: ['resources/**'],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/**', '**/vendor/**', '**/node_modules/**'],
        },
    },
});