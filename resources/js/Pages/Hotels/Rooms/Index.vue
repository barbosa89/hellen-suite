<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowLeftIcon,
    BuildingOffice2Icon,
    RectangleGroupIcon,
} from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

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
            t('rooms.pages.index.heading', {
                hotel: props.hotel.business_name,
            })
        "
    />

    <DefaultLayout :hotel="props.hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('rooms.title')"
                    :description="
                        t('rooms.pages.index.description', {
                            hotel: props.hotel.business_name,
                        })
                    "
                >
                    <template #actions>
                        <ActionLink
                            :href="
                                route('hotels.management.index', props.hotel.id)
                            "
                            variant="ghost"
                        >
                            <ArrowLeftIcon class="h-4 w-4" />
                            {{ t('app.navigation.overview') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <section
                    class="flex min-h-[26rem] flex-col items-center justify-center rounded-2xl bg-white px-6 py-16 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 relative flex h-16 w-16 items-center justify-center rounded-2xl"
                    >
                        <RectangleGroupIcon class="h-8 w-8" />
                        <BuildingOffice2Icon
                            class="absolute -end-2 -bottom-2 h-7 w-7 rounded-lg bg-white p-1.5 text-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-neutral-300"
                        />
                    </span>
                    <h2
                        class="mt-6 text-xl font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white"
                    >
                        {{ t('rooms.pages.index.empty_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('rooms.pages.index.empty') }}
                    </p>
                </section>
            </div>
        </div>
    </DefaultLayout>
</template>
