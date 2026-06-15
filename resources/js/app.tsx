import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import { configureEcho } from '@laravel/echo-react';

import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    strictMode: true,
    progress: {
        color: '#4B5563',
    },
    defaults: {
        visitOptions: () => {
            return { viewTransition: true };
        },
    },
});

// This will set light / dark mode on load...
initializeTheme();

configureEcho({
    broadcaster: typeof window !== 'undefined' ? 'reverb' : 'null',
});
