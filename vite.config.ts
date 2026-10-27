import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig, loadEnv, type ServerOptions } from 'vite';
import run from 'vite-plugin-run';

const runsInDevContainer = process.env.DEV_VITE_CONTAINER === '1';

function devContainerServer(mode: string): ServerOptions | undefined {
    const viteOrigin = process.env.DEV_VITE_ORIGIN;
    const appUrl = loadEnv(mode, process.cwd(), 'APP_URL').APP_URL;

    if (!runsInDevContainer || !viteOrigin || !appUrl) {
        return undefined;
    }

    const viteUrl = new URL(viteOrigin);

    return {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: viteUrl.origin,
        allowedHosts: [viteUrl.hostname],
        cors: { origin: [new URL(appUrl).origin, /^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/] },
        hmr: { protocol: 'wss', host: viteUrl.hostname, clientPort: 443 },
    };
}

const phpCodegenPlugins = runsInDevContainer
    ? []
    : [
          wayfinder(),
          run([
              {
                  name: 'typescript-transform',
                  run: ['php', 'artisan', 'typescript:transform'],
                  pattern: ['app/Data/**/*.php'],
              },
          ]),
      ];

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
        }),
        inertia(),
        react(),
        tailwindcss(),
        ...phpCodegenPlugins,
    ],
    server: devContainerServer(mode),
}));
