import { createI18n } from 'vue-i18n';
import locales from '@/lang/locales.js';

const documentLocale = globalThis.document?.documentElement?.lang?.split('-')[0];
const locale = Object.hasOwn(locales, documentLocale)
    ? documentLocale
    : 'en';

const i18n = createI18n({
    locale,
    fallbackLocale: 'en',
    legacy: false,
    warnHtmlMessage: false,
    messages: locales,
});

const createAppI18n = () => i18n;

export { createAppI18n, i18n };
