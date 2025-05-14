import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import inertiaServer from '@inertiajs/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import route from './route';

const { server } = inertiaServer;

server((page) =>
  createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - TravelManager`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ App, props, plugin }) {
      global.route = route;
      return createSSRApp({ render: () => h(App, props) })
        .use(plugin);
    },
  })
);