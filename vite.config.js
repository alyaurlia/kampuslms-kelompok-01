import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/components/layout.css',
                'resources/css/components/dashboard.css',
                'resources/css/components/courses.css',
                'resources/css/courses/forms.css',
                'resources/css/users/users.css',
                'resources/css/errors/errors.css',
                'resources/css/tentang.css',
                'resources/css/welcome.css',
            ],
            refresh: true,
        }),
    ],
});