import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/upload.js',
                'resources/js/viewer.js',
                'resources/js/editor.js',
                'resources/js/uploads-page.js',
                'resources/js/claude-story.js',
                'resources/js/uth-anim.js',
                'resources/js/who-uses.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
