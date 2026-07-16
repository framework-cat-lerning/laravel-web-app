import { createInertiaApp } from "@inertiajs/react";
import type { ResolvedComponent } from "@inertiajs/react";
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import type { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import MainLayout from "@/components/layouts/MainLayout";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name) => {
        const page = await resolvePageComponent<{ default: ResolvedComponent }>(
            `./pages/${name}.tsx`,
            import.meta.glob<{ default: ResolvedComponent }>("./pages/**/*.tsx")
        );

        // 認証後ページは MainLayout をデフォルトで適用する
        // ゲストページ側で `Page.layout = null` を指定すると除外できる
        if (page.default.layout === undefined) {
            page.default.layout = (pageContent: ReactNode) => (
                <MainLayout>{pageContent}</MainLayout>
            );
        }

        return page.default;
    },
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
