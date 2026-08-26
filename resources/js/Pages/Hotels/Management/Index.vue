<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import HotelAvatar from '@/Components/HotelAvatar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowTopRightOnSquareIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    CheckCircleIcon,
    EnvelopeIcon,
    IdentificationIcon,
    MapPinIcon,
    PencilSquareIcon,
    PhoneIcon,
    RectangleGroupIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const profileFields = [
    'business_name',
    'tin',
    'address',
    'phone',
    'mobile',
    'email',
    'image',
];

const completedFields = computed(
    () => profileFields.filter((field) => Boolean(props.hotel[field])).length,
);

const completionPercentage = computed(() =>
    Math.round((completedFields.value / profileFields.length) * 100),
);
</script>

<template>
    <Head :title="props.hotel.business_name" />

    <DefaultLayout :hotel="props.hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('hotels.management.heading')"
                    :description="
                        t('hotels.management.description', {
                            name: props.hotel.business_name,
                        })
                    "
                >
                    <template #actions>
                        <ActionLink
                            :href="route('hotels.edit', props.hotel.id)"
                            variant="secondary"
                        >
                            <PencilSquareIcon class="h-4 w-4" />
                            {{ t('hotels.actions.edit') }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <div
                    class="grid gap-5 xl:grid-cols-[minmax(0,1.3fr)_minmax(20rem,0.7fr)] 2xl:grid-cols-[minmax(0,1.15fr)_minmax(22rem,0.65fr)_minmax(20rem,0.55fr)]"
                >
                    <section
                        class="overflow-hidden rounded-2xl bg-white shadow-sm 2xl:col-span-2 dark:bg-neutral-900"
                    >
                        <div
                            class="flex flex-col gap-6 p-5 sm:p-7 lg:flex-row lg:items-center lg:justify-between lg:p-8"
                        >
                            <div class="flex min-w-0 items-center gap-5">
                                <HotelAvatar :hotel="props.hotel" size="xl" />
                                <div class="min-w-0">
                                    <h2
                                        class="truncate text-2xl font-semibold tracking-[-0.025em] text-neutral-950 dark:text-white"
                                    >
                                        {{ props.hotel.business_name }}
                                    </h2>
                                    <p
                                        class="mt-2 flex items-center gap-2 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                    >
                                        <IdentificationIcon class="h-4 w-4" />
                                        {{ props.hotel.tin }}
                                    </p>
                                    <p
                                        v-if="props.hotel.address"
                                        class="mt-1.5 flex items-start gap-2 text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        <MapPinIcon
                                            class="mt-0.5 h-4 w-4 shrink-0"
                                        />
                                        {{ props.hotel.address }}
                                    </p>
                                </div>
                            </div>

                            <ActionLink
                                :href="route('hotels.show', props.hotel.id)"
                                variant="ghost"
                            >
                                <BuildingOffice2Icon class="h-4 w-4" />
                                {{ t('hotels.actions.view') }}
                            </ActionLink>
                        </div>

                        <div
                            class="grid border-t border-neutral-200 sm:grid-cols-2 dark:border-neutral-800"
                        >
                            <div
                                class="flex items-center gap-3 px-5 py-4 sm:px-7 lg:px-8"
                            >
                                <EnvelopeIcon
                                    class="h-5 w-5 shrink-0 text-neutral-500"
                                />
                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{ t('hotels.fields.email.label') }}
                                    </p>
                                    <p
                                        class="mt-1 truncate text-sm font-medium text-neutral-900 dark:text-neutral-100"
                                    >
                                        {{
                                            props.hotel.email ??
                                            t('app.not_provided')
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-3 border-t border-neutral-200 px-5 py-4 sm:border-s sm:border-t-0 sm:px-7 lg:px-8 dark:border-neutral-800"
                            >
                                <PhoneIcon
                                    class="h-5 w-5 shrink-0 text-neutral-500"
                                />
                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-semibold text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{
                                            t('hotels.management.contact_phone')
                                        }}
                                    </p>
                                    <p
                                        class="mt-1 truncate text-sm font-medium text-neutral-900 tabular-nums dark:text-neutral-100"
                                    >
                                        {{
                                            props.hotel.phone ??
                                            props.hotel.mobile ??
                                            t('app.not_provided')
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section
                        class="rounded-2xl bg-neutral-900 p-5 text-white shadow-sm sm:p-7 dark:bg-white dark:text-neutral-950"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold">
                                {{ t('hotels.management.profile_status') }}
                            </h2>
                            <CheckCircleIcon
                                class="text-primary-400 dark:text-primary-600 h-5 w-5"
                            />
                        </div>
                        <p
                            class="mt-3 text-sm leading-6 text-neutral-300 dark:text-neutral-600"
                        >
                            {{
                                t('hotels.management.profile_progress', {
                                    completed: completedFields,
                                    total: profileFields.length,
                                })
                            }}
                        </p>
                        <div
                            class="mt-5 h-1.5 overflow-hidden rounded-full bg-neutral-700 dark:bg-neutral-200"
                            aria-hidden="true"
                        >
                            <div
                                class="bg-primary-400 dark:bg-primary-600 h-full rounded-full transition-[width] duration-300 ease-out motion-reduce:transition-none"
                                :style="{ width: `${completionPercentage}%` }"
                            />
                        </div>
                        <p
                            class="mt-2 text-end text-xs font-semibold text-neutral-300 tabular-nums dark:text-neutral-600"
                        >
                            {{ completionPercentage }}%
                        </p>
                        <ActionLink
                            :href="route('hotels.edit', props.hotel.id)"
                            variant="secondary"
                            class="mt-5 w-full dark:border-neutral-300 dark:bg-white"
                        >
                            <PencilSquareIcon class="h-4 w-4" />
                            {{ t('hotels.management.complete_profile') }}
                        </ActionLink>
                    </section>

                    <section
                        class="overflow-hidden rounded-2xl bg-white shadow-sm xl:col-span-2 2xl:col-span-3 dark:bg-neutral-900"
                    >
                        <div
                            class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                        >
                            <h2
                                class="text-lg font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ t('hotels.management.modules') }}
                            </h2>
                            <p
                                class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                            >
                                {{ t('hotels.management.modules_description') }}
                            </p>
                        </div>

                        <div
                            class="grid divide-y divide-neutral-200 dark:divide-neutral-800"
                        >
                            <div
                                class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                            >
                                <div class="flex items-center gap-4">
                                    <span
                                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-11 w-11 items-center justify-center rounded-xl"
                                    >
                                        <RectangleGroupIcon class="h-5 w-5" />
                                    </span>
                                    <div>
                                        <h3
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{ t('rooms.title') }}
                                        </h3>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{
                                                t(
                                                    'rooms.management.description',
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <ActionLink
                                    :href="
                                        route(
                                            'hotels.rooms.index',
                                            props.hotel.id,
                                        )
                                    "
                                    prefetch
                                >
                                    {{ t('hotels.management.open_module') }}
                                    <ArrowTopRightOnSquareIcon
                                        class="h-4 w-4"
                                    />
                                </ActionLink>
                            </div>
                            <div
                                class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                            >
                                <div class="flex items-center gap-4">
                                    <span
                                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-11 w-11 items-center justify-center rounded-xl"
                                        ><UserGroupIcon class="h-5 w-5"
                                    /></span>
                                    <div>
                                        <h3
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{ t('guests.title') }}
                                        </h3>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{
                                                t(
                                                    'guests.pages.index.description',
                                                    {
                                                        hotel: props.hotel
                                                            .business_name,
                                                    },
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <ActionLink
                                    :href="
                                        route(
                                            'hotels.guests.index',
                                            props.hotel.id,
                                        )
                                    "
                                    prefetch
                                    >{{ t('hotels.management.open_module')
                                    }}<ArrowTopRightOnSquareIcon
                                        class="h-4 w-4"
                                /></ActionLink>
                            </div>
                            <div
                                class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                            >
                                <div class="flex items-center gap-4">
                                    <span
                                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-11 w-11 items-center justify-center rounded-xl"
                                        ><CalendarDaysIcon class="h-5 w-5"
                                    /></span>
                                    <div>
                                        <h3
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{ t('stays.title') }}
                                        </h3>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{
                                                t(
                                                    'stays.pages.index.description',
                                                    {
                                                        hotel: props.hotel
                                                            .business_name,
                                                    },
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <ActionLink
                                    :href="
                                        route(
                                            'hotels.stays.index',
                                            props.hotel.id,
                                        )
                                    "
                                    prefetch
                                    >{{ t('hotels.management.open_module')
                                    }}<ArrowTopRightOnSquareIcon
                                        class="h-4 w-4"
                                /></ActionLink>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
