<script setup>
import {
    BuildingOffice2Icon,
    RectangleGroupIcon,
} from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';

const { t } = useI18n();

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head
        :title="
            t('hotels.pages.rooms.heading', {
                hotel: props.hotel.business_name,
            })
        "
    />

    <DefaultLayout :hotel="props.hotel">
        <template #header>
            <div class="flex items-center gap-3">
                <RectangleGroupIcon
                    class="text-primary-600 dark:text-primary-400 h-6 w-6"
                />
                <div>
                    <h2
                        class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        {{
                            t('hotels.pages.rooms.heading', {
                                hotel: props.hotel.business_name,
                            })
                        }}
                    </h2>
                    <p
                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                    >
                        {{ props.hotel.business_name }}
                    </p>
                </div>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div
                    class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
                >
                    <div
                        class="flex items-center gap-3 text-neutral-700 dark:text-neutral-300"
                    >
                        <BuildingOffice2Icon
                            class="text-primary-600 dark:text-primary-400 h-6 w-6 shrink-0"
                        />
                        <p class="text-sm">
                            {{ t('hotels.pages.rooms.empty') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
