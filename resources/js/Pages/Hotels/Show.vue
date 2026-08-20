<script setup>
import HotelLayout from '@/Layouts/HotelLayout.vue';
import {
    BuildingOffice2Icon,
    EnvelopeIcon,
    MapPinIcon,
    PhoneIcon,
    RectangleGroupIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
});

const primaryLinkClasses =
    'inline-flex items-center gap-2 rounded-md border border-transparent bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-primary-800 focus:bg-primary-800 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:outline-hidden active:bg-primary-900 dark:bg-primary-400 dark:text-neutral-950 dark:hover:bg-primary-500 dark:focus:bg-primary-500 dark:active:bg-primary-600 dark:focus:ring-offset-neutral-950';

const fieldClasses =
    'col-span-1 rounded-lg bg-neutral-50 p-4 ring-1 ring-neutral-200 dark:bg-neutral-900 dark:ring-neutral-700';
</script>

<template>
    <Head :title="props.hotel.business_name" />

    <HotelLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2
                    class="text-xl leading-tight font-semibold text-neutral-800 dark:text-neutral-200"
                >
                    {{ props.hotel.business_name }}
                </h2>

                <span
                    class="bg-primary-50 text-primary-800 ring-primary-200 dark:bg-primary-900/30 dark:text-primary-300 dark:ring-primary-800 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1"
                >
                    <span
                        class="bg-primary-600 dark:bg-primary-400 h-1.5 w-1.5 rounded-full"
                    ></span>
                    #{{ props.hotel.id }}
                </span>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <div
                    class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
                >
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div :class="fieldClasses">
                                <dt
                                    class="flex items-center gap-1.5 text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                >
                                    <BuildingOffice2Icon class="h-4 w-4" />
                                    {{ t('hotels.fields.business_name.label') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm font-medium text-neutral-900 dark:text-neutral-100"
                                >
                                    {{ props.hotel.business_name }}
                                </dd>
                            </div>

                            <div :class="fieldClasses">
                                <dt
                                    class="flex items-center gap-1.5 text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                >
                                    {{ t('hotels.fields.tin.label') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm font-medium text-neutral-900 tabular-nums dark:text-neutral-100"
                                >
                                    {{ props.hotel.tin }}
                                </dd>
                            </div>

                            <div :class="fieldClasses">
                                <dt
                                    class="flex items-center gap-1.5 text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                >
                                    <MapPinIcon class="h-4 w-4" />
                                    {{ t('hotels.fields.address.label') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm text-neutral-900 dark:text-neutral-100"
                                >
                                    {{ props.hotel.address ?? '—' }}
                                </dd>
                            </div>

                            <div :class="fieldClasses">
                                <dt
                                    class="flex items-center gap-1.5 text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                >
                                    <PhoneIcon class="h-4 w-4" />
                                    {{ t('hotels.fields.phone.label') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm text-neutral-900 tabular-nums dark:text-neutral-100"
                                >
                                    {{ props.hotel.phone ?? '—' }}
                                </dd>
                            </div>

                            <div :class="fieldClasses">
                                <dt
                                    class="flex items-center gap-1.5 text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                >
                                    <PhoneIcon class="h-4 w-4" />
                                    {{ t('hotels.fields.mobile.label') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm text-neutral-900 tabular-nums dark:text-neutral-100"
                                >
                                    {{ props.hotel.mobile ?? '—' }}
                                </dd>
                            </div>

                            <div :class="fieldClasses">
                                <dt
                                    class="flex items-center gap-1.5 text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                >
                                    <EnvelopeIcon class="h-4 w-4" />
                                    {{ t('hotels.fields.email.label') }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm text-neutral-900 dark:text-neutral-100"
                                >
                                    {{ props.hotel.email ?? '—' }}
                                </dd>
                            </div>
                        </dl>

                        <div
                            v-if="props.hotel.image"
                            class="mt-4 rounded-lg bg-neutral-50 p-4 ring-1 ring-neutral-200 dark:bg-neutral-900 dark:ring-neutral-700"
                        >
                            <dt
                                class="text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                            >
                                {{ t('hotels.fields.image.label') }}
                            </dt>
                            <img
                                :src="props.hotel.image"
                                :alt="props.hotel.business_name"
                                class="mt-3 aspect-video w-full rounded-md object-cover"
                            />
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between gap-3 border-t border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900"
                    >
                        <Link
                            :href="route('hotels.index')"
                            class="focus:ring-primary-500 inline-flex items-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold tracking-widest text-neutral-700 uppercase shadow-xs transition duration-150 ease-in-out hover:bg-neutral-50 focus:ring-2 focus:ring-offset-2 focus:outline-hidden dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-950"
                        >
                            {{ t('app.back') }}
                        </Link>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="
                                    route('hotels.rooms.index', props.hotel.id)
                                "
                                :class="primaryLinkClasses"
                            >
                                <RectangleGroupIcon class="h-4 w-4" />
                                {{ t('hotels.actions.rooms') }}
                            </Link>

                            <Link
                                :href="route('hotels.edit', props.hotel.id)"
                                :class="primaryLinkClasses"
                            >
                                {{ t('hotels.actions.edit') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HotelLayout>
</template>
