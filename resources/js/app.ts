import '../css/app.css';
import './bootstrap';
import 'floating-vue/dist/style.css';

import { createInertiaApp } from '@inertiajs/vue3';
import FloatingVue from 'floating-vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const configuredAppName = import.meta.env.VITE_APP_NAME;
const appName = !configuredAppName || configuredAppName === 'Laravel'
    ? 'CarsDo'
    : configuredAppName;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(FloatingVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
