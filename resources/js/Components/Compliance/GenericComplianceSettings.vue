<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <div
        class="rounded-xl bg-neutral-50 p-4 text-sm leading-6 text-neutral-600 dark:bg-neutral-950/60 dark:text-neutral-400"
    >
        <p class="font-semibold text-neutral-950 dark:text-white">
            {{ t('hotels.form.compliance.generic_title') }}
        </p>
        <p class="mt-1">
            {{ t('hotels.form.compliance.generic_description') }}
        </p>
    </div>
</template>
