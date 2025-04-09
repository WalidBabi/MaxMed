import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import compressionPlugin from 'vite-plugin-compression';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        compressionPlugin({
            algorithm: 'gzip',
            ext: '.gz',
            deleteOriginFile: false,
        }),
    ],
    build: {
        // Enable minification and tree shaking
        minify: 'terser',
        cssMinify: true,
        // Enable code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
                // Optimize chunk size
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        // Enable gzip compression
        terserOptions: {
            compress: {
                drop_console: false,
            },
        },
    },
});
