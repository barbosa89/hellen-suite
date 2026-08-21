<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    pagination: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-4 border-t border-neutral-200 bg-neutral-50 px-5 py-4 sm:flex-row xl:px-6 dark:border-neutral-800 dark:bg-neutral-950/60"
    >
        <p class="text-sm text-neutral-600 tabular-nums dark:text-neutral-400">
            {{ t('app.pagination.showing') }}
            <span class="font-semibold text-neutral-900 dark:text-neutral-100">
                {{ pagination?.from ?? 0 }}
            </span>
            {{ t('app.pagination.to') }}
            <span class="font-semibold text-neutral-900 dark:text-neutral-100">
                {{ pagination?.to ?? 0 }}
            </span>
            {{ t('app.pagination.of') }}
            <span class="font-semibold text-neutral-900 dark:text-neutral-100">
                {{ pagination?.total ?? 0 }}
            </span>
        </p>

        <nav
            class="flex flex-wrap items-center justify-center gap-1"
            :aria-label="t('app.pagination.pagination')"
        >
            <template v-for="link in pagination?.links ?? []" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="focus-visible:ring-primary-500 min-h-11 min-w-11 rounded-lg px-3 py-2 text-center text-sm font-semibold transition-colors duration-150 focus-visible:ring-2 focus-visible:outline-hidden motion-reduce:transition-none sm:min-h-9 sm:min-w-9"
                    :class="
                        link.active
                            ? 'bg-primary-800 dark:bg-primary-300 text-white dark:text-neutral-950'
                            : 'text-neutral-600 hover:bg-white hover:text-neutral-950 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white'
                    "
                >
                    <span v-html="link.label" />
                </Link>
                <span
                    v-else
                    class="min-h-11 min-w-11 cursor-default rounded-lg px-3 py-2 text-center text-sm font-medium text-neutral-400 sm:min-h-9 sm:min-w-9 dark:text-neutral-600"
                    v-html="link.label"
                />
            </template>
        </nav>
    </div>
</template>
