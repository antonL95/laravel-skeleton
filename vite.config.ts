import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import run from 'vite-plugin-run';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react(),
        tailwindcss(),
        wayfinder({
            formVariants: true,
            path: "resources/js/wayfinder",
        }),
        run([
            {
                name: "typescript-transform",
                run: ["php", "artisan", "typescript:transform"],
                pattern: ["app/Data/**/*.php"],
            },
        ]),
    ],
    esbuild: {
        jsx: 'automatic',
    },
});
