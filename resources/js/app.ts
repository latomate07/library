import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// PrimeVue imports
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css';

// PrimeVue components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import ConfirmDialog from 'primevue/confirmdialog';
import ConfirmationService from 'primevue/confirmationservice';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';
import Tag from 'primevue/tag';
import Toolbar from 'primevue/toolbar';
import Card from 'primevue/card';
import Divider from 'primevue/divider';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: false,
                    }
                }
            })
            .use(ToastService)
            .use(ConfirmationService);

        // Register PrimeVue components globally
        app.component('Button', Button);
        app.component('InputText', InputText);
        app.component('Textarea', Textarea);
        app.component('Dropdown', Dropdown);
        app.component('DataTable', DataTable);
        app.component('Column', Column);
        app.component('Dialog', Dialog);
        app.component('Toast', Toast);
        app.component('ConfirmDialog', ConfirmDialog);
        app.component('Calendar', Calendar);
        app.component('InputNumber', InputNumber);
        app.component('Tag', Tag);
        app.component('Toolbar', Toolbar);
        app.component('Card', Card);
        app.component('Divider', Divider);
        app.component('InputGroup', InputGroup);
        app.component('InputGroupAddon', InputGroupAddon);

        app.mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});