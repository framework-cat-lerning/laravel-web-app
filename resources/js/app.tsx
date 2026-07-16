import { createInertiaApp  } from "@inertiajs/react";
import type {ResolvedComponent} from "@inertiajs/react";
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent<ResolvedComponent>(
            `./pages/${name}.tsx`,
            import.meta.glob<ResolvedComponent>("./pages/**/*.tsx")
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        // LocalizationProvider を Inertia.js の App コンポーネントに組み込む
        root.render(
            <LocalizationProvider dateAdapter={AdapterDayjs}>
                <App {...props} />
            </LocalizationProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});
