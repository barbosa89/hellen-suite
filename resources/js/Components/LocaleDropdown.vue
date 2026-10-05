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
                class="focus:ring-primary-500 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-md text-neutral-600 transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-900 focus:ring-2 focus:ring-offset-2 focus:outline-hidden motion-reduce:transition-none sm:h-9 sm:w-9 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100 dark:focus:ring-offset-neutral-900"
                :aria-label="t('app.language')"
            >
                <LanguageIcon class="h-5 w-5" />
            </button>
        </template>

        <template #content>
            <div class="py-1" role="menu" aria-orientation="vertical">
                <button
                    v-for="item in locales"
                    :key="item.code"
                    type="button"
                    role="menuitemradio"
                    :aria-checked="item.code === locale"
                    class="group flex min-h-11 w-full items-center justify-between gap-3 px-4 py-2.5 text-start text-sm leading-5 transition duration-150 ease-in-out focus:outline-hidden motion-reduce:transition-none"
                    :class="
                        item.code === locale
                            ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-50'
                            : 'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900 focus:bg-neutral-50 dark:text-neutral-400 dark:hover:bg-neutral-800/50 dark:hover:text-neutral-100 dark:focus:bg-neutral-800/50'
                    "
                    @click="changeLocale(item.code)"
                >
                    <span
                        class="font-medium"
                        :class="
                            item.code === locale
                                ? 'font-semibold'
                                : 'font-normal group-hover:font-medium'
                        "
                    >
                        {{ item.label }}
                    </span>
                    <CheckIcon
                        v-if="item.code === locale"
                        class="text-primary-600 dark:text-primary-400 h-4 w-4 shrink-0"
                        aria-hidden="true"
                    />
                </button>
            </div>
        </template>
    </Dropdown>
</template>
