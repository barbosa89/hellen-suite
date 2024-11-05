import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import i18n from 'laravel-vue-i18n/vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/panel.css',
                'resources/js/app.js',
                'resources/js/panel.js',
                'resources/css/landing.css',
                'resources/js/landing.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        i18n(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
})
