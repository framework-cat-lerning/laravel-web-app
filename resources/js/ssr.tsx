import { createInertiaApp } from "@inertiajs/react";
import type { ResolvedComponent } from "@inertiajs/react";
import createServer from "@inertiajs/react/server";
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import type { ReactNode } from "react";
import ReactDOMServer from "react-dom/server";
import MainLayout from "@/components/layouts/MainLayout";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
  createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name) => {
      const page = await resolvePageComponent<{ default: ResolvedComponent }>(
        `./pages/${name}.tsx`,
        import.meta.glob<{ default: ResolvedComponent }>("./pages/**/*.tsx")
      );

      if (page.default.layout === undefined) {
        page.default.layout = (pageContent: ReactNode) => (
          <MainLayout>{pageContent}</MainLayout>
        );
      }

      return page.default;
    },
    setup: ({ App, props }) => (
      <LocalizationProvider dateAdapter={AdapterDayjs}>
        <App {...props} />
      </LocalizationProvider>
    ),
  })
);