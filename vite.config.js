import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import { fileURLToPath } from 'url';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            'webmidi': fileURLToPath(new URL('./resources/js/webmidi-stub.js', import.meta.url)),
            'events': fileURLToPath(new URL('./resources/js/events-stub.js', import.meta.url))
        }
    },
    server: {
        port: parseInt(process.env.VITE_PORT || 5176),
        strictPort: false, // Allow auto-switch ports if conflict
        hmr: {
            host: 'localhost'
        }
    },
    build: {
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            }
        }
    },
    base: process.env.NODE_ENV === 'production' ? 'https://chordhound.com/' : '/',
});