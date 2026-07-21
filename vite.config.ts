import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from "vitest/config";
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (
                        id.includes('node_modules/react-dom')
                        || id.includes('node_modules/react-router')
                        || /node_modules\/react\//.test(id)
                    ) {
                        return 'react-vendor'
                    }
                    if (id.includes('node_modules/@mui/x-date-pickers') || id.includes('node_modules/dayjs')) {
                        return 'mui-datepicker'
                    }
                    if (id.includes('node_modules/@mui/icons-material')) {
                        return 'mui-icons'
                    }
                    if (id.includes('node_modules/@emotion/')) {
                        return 'mui-emotion'
                    }
                    if (id.includes('node_modules/@mui/x-charts')) {
                        return 'mui-x-charts'
                    }
                    if (id.includes('node_modules/@mui/x-data-grid')) {
                        return 'mui-x-data-grid'
                    }
                    if (id.includes('node_modules/@mui/x-date-pickers')) {
                        return 'mui-x-date-pickers'
                    }
                    if (id.includes('node_modules/@mui/x-tree-view')) {
                        return 'mui-x-tree-view'
                    }
                    if (id.includes('node_modules/@mui/') || id.includes('node_modules/@popperjs/')) {
                        return 'mui-core'
                    }
                    if (id.includes('node_modules/react-hook-form') || id.includes('node_modules/zod') || id.includes('node_modules/@hookform/')) {
                        return 'form'
                    }
                },
            },
        },
    },
    test: {
        environment: "jsdom",
        globals: true,
        setupFiles: "resources/js/tests/setup.ts",
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
});
