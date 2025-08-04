import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build/', // Ensures assets are loaded relatively (avoids hardcoding http://)
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5175,
        strictPort: true,
        hmr: {
            protocol: 'wss',
            host: 'booking.fury.me.uk',
            port: 5175,
        },
    },
});

