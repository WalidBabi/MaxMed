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
        // Gzip compression
        compressionPlugin({
            algorithm: 'gzip',
            ext: '.gz',
            threshold: 1024, // Only compress files > 1kb
            deleteOriginFile: false,
        }),
        // Brotli compression (better than gzip)
        compressionPlugin({
            algorithm: 'brotliCompress',
            ext: '.br',
            threshold: 1024,
            deleteOriginFile: false,
            compressionOptions: { level: 11 }, // Max compression level
        }),
    ],
    build: {
        // Enable minification and tree shaking
        minify: 'terser',
        cssMinify: true,
        cssCodeSplit: true,
        // Extract CSS into separate files
        cssTarget: 'es2015',
        // Enable source maps for production builds
        sourcemap: false,
        // Enable code splitting
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Group vendor libraries into a separate chunk
                    if (id.includes('node_modules')) {
                        // Further split large libraries into their own chunks
                        if (id.includes('alpinejs')) {
                            return 'vendor-alpine';
                        }
                        return 'vendor';
                    }
                    
                    // Split CSS by pages
                    if (id.includes('/css/pages/')) {
                        const name = id.split('/css/pages/')[1].split('.')[0];
                        return `css-${name}`;
                    }
                },
                // Optimize chunk size
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    // Put each asset type in its own directory
                    const extType = assetInfo.name.split('.').at(1);
                    if (/png|jpe?g|svg|gif|webp|avif|ico/i.test(extType)) {
                        return 'assets/images/[name]-[hash][extname]';
                    }
                    if (/css/i.test(extType)) {
                        return 'assets/css/[name]-[hash][extname]';
                    }
                    if (/woff|woff2|eot|ttf|otf/i.test(extType)) {
                        return 'assets/fonts/[name]-[hash][extname]';
                    }
                    return 'assets/[ext]/[name]-[hash][extname]';
                },
            },
        },
        // Terser options for more aggressive optimization
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
            mangle: {
                safari10: true,
            },
            format: {
                comments: false,
            },
        },
        // Set chunk size warnings thresholds
        chunkSizeWarningLimit: 1000,
    },
    optimizeDeps: {
        include: ['alpinejs'],
    },
});
