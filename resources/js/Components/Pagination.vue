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
        class="flex flex-col items-center justify-between gap-4 border-t border-neutral-200 px-6 py-4 sm:flex-row dark:border-neutral-700"
    >
        <p class="text-sm text-neutral-600 dark:text-neutral-400">
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
                    class="focus:ring-primary-500 min-w-8 rounded-md px-3 py-2 text-sm font-medium transition duration-150 ease-in-out focus:ring-2 focus:outline-hidden"
                    :class="
                        link.active
                            ? 'bg-primary-500 dark:bg-primary-400 text-white dark:text-neutral-950'
                            : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-neutral-100'
                    "
                >
                    <span v-html="link.label" />
                </Link>
                <span
                    v-else
                    class="min-w-8 cursor-default rounded-md px-3 py-2 text-sm font-medium text-neutral-400 dark:text-neutral-600"
                    v-html="link.label"
                />
            </template>
        </nav>
    </div>
</template>
