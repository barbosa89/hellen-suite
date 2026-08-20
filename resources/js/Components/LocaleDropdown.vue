<script setup>
import { CheckIcon, LanguageIcon } from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Dropdown from '@/Components/Dropdown.vue';
import { i18n } from '@/lang/i18n.js';

const { t, locale } = useI18n();

const locales = [
    { code: 'en', label: 'English' },
    { code: 'es', label: 'Español' },
];

function changeLocale(code) {
    if (code === locale.value) {
        return;
    }

    document.documentElement.lang = code;
    i18n.global.locale.value = code;

    router.post(
        route('locale.update'),
        { locale: code },
        { preserveScroll: true, preserveState: true },
    );
}
</script>

<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button
                type="button"
                class="focus:ring-primary-500 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-neutral-500 transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-700 focus:ring-2 focus:outline-hidden dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100"
                :aria-label="t('app.language')"
            >
                <LanguageIcon class="h-5 w-5" />
            </button>
        </template>

        <template #content>
            <button
                v-for="item in locales"
                :key="item.code"
                type="button"
                class="flex w-full items-center justify-between gap-2 px-4 py-2 text-start text-sm leading-5 text-neutral-700 transition duration-150 ease-in-out hover:bg-neutral-100 focus:bg-neutral-100 focus:outline-hidden dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                :class="
                    item.code === locale
                        ? 'text-primary-700 dark:text-primary-300 font-semibold'
                        : ''
                "
                @click="changeLocale(item.code)"
            >
                {{ item.label }}
                <CheckIcon
                    v-if="item.code === locale"
                    class="text-primary-600 dark:text-primary-400 h-4 w-4"
                />
            </button>
        </template>
    </Dropdown>
</template>
