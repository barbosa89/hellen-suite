import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createAppI18n } from '@/lang/i18n.js';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    withApp(app) {
        app.use(createAppI18n()).use(ZiggyVue);
    },
    progress: {
        color: '#00bcd4',
    },
});
