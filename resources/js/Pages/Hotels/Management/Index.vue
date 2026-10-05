<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import HotelAvatar from '@/Pages/Hotels/Components/HotelAvatar.vue';
import OccupancyForecastChart from '@/Pages/Hotels/Management/Components/OccupancyForecastChart.vue';
import {
    ArrowTrendingUpIcon,
    BanknotesIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    ChartBarIcon,
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
    dashboard: {
        type: Object,
        required: true,
    },
});

const { t, locale } = useI18n();

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

const metrics = computed(() => props.dashboard.metrics);

const localeCode = computed(() => (locale.value === 'es' ? 'es-CO' : 'en-US'));

function money(value, minor = false) {
    if (!props.dashboard.currency) {
        return t('hotels.management.metrics.currency_unavailable');
    }

    return new Intl.NumberFormat(localeCode.value, {
        style: 'currency',
        currency: props.dashboard.currency,
        maximumFractionDigits: 2,
    }).format(minor ? Number(value) / 100 : Number(value));
}
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

                    <section class="xl:col-span-2 2xl:col-span-3">
                        <div
                            class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"
                        >
                            <div>
                                <p
                                    class="text-primary-700 dark:text-primary-300 text-xs font-semibold tracking-[0.16em] uppercase"
                                >
                                    {{ t('hotels.management.metrics.eyebrow') }}
                                </p>
                                <h2
                                    class="mt-2 text-2xl font-semibold tracking-[-0.025em] text-neutral-950 dark:text-white"
                                >
                                    {{ t('hotels.management.metrics.title') }}
                                </h2>
                            </div>
                            <p
                                class="max-w-xl text-sm leading-6 text-neutral-600 sm:text-end dark:text-neutral-400"
                            >
                                {{ t('hotels.management.metrics.description') }}
                            </p>
                        </div>

                        <div
                            class="grid gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(19rem,0.65fr)]"
                        >
                            <div class="grid gap-5">
                                <div
                                    class="overflow-hidden rounded-2xl bg-neutral-900 text-white shadow-sm dark:bg-white dark:text-neutral-950"
                                >
                                    <div class="p-5 sm:p-7">
                                        <div
                                            class="flex items-start justify-between gap-5"
                                        >
                                            <div>
                                                <p
                                                    class="flex items-center gap-2 text-sm font-medium text-neutral-300 dark:text-neutral-600"
                                                >
                                                    <ChartBarIcon
                                                        class="h-5 w-5"
                                                    />
                                                    {{
                                                        t(
                                                            'hotels.management.metrics.current_occupancy',
                                                        )
                                                    }}
                                                </p>
                                                <p
                                                    class="mt-3 text-5xl font-semibold tracking-[-0.05em] tabular-nums sm:text-6xl"
                                                >
                                                    {{ metrics.occupancyRate
                                                    }}<span
                                                        class="ms-1 text-2xl text-neutral-400 dark:text-neutral-500"
                                                        >%</span
                                                    >
                                                </p>
                                            </div>
                                            <span
                                                class="bg-primary-400/15 text-primary-300 dark:bg-primary-600/10 dark:text-primary-700 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                            >
                                                <ArrowTrendingUpIcon
                                                    class="h-5 w-5"
                                                />
                                            </span>
                                        </div>

                                        <p
                                            class="mt-4 text-sm text-neutral-300 tabular-nums dark:text-neutral-600"
                                        >
                                            {{
                                                t(
                                                    'hotels.management.metrics.rooms_occupied',
                                                    {
                                                        occupied:
                                                            metrics.occupiedRooms,
                                                        total: metrics.activeRooms,
                                                    },
                                                )
                                            }}
                                        </p>
                                        <div
                                            class="mt-4 h-2 overflow-hidden rounded-full bg-neutral-700 dark:bg-neutral-200"
                                        >
                                            <div
                                                class="bg-primary-400 dark:bg-primary-600 h-full rounded-full transition-[width] duration-500 motion-reduce:transition-none"
                                                :style="{
                                                    width: `${Math.min(metrics.occupancyRate, 100)}%`,
                                                }"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="grid border-t border-neutral-700 sm:grid-cols-3 dark:border-neutral-200"
                                    >
                                        <div class="px-5 py-4 sm:px-7">
                                            <p
                                                class="text-xs font-semibold text-neutral-400 dark:text-neutral-500"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.adr',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1.5 font-semibold tabular-nums"
                                            >
                                                {{ money(metrics.adr) }}
                                            </p>
                                        </div>
                                        <div
                                            class="border-t border-neutral-700 px-5 py-4 sm:border-s sm:border-t-0 sm:px-7 dark:border-neutral-200"
                                        >
                                            <p
                                                class="text-xs font-semibold text-neutral-400 dark:text-neutral-500"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.revpar',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1.5 font-semibold tabular-nums"
                                            >
                                                {{ money(metrics.revPar) }}
                                            </p>
                                        </div>
                                        <div
                                            class="border-t border-neutral-700 px-5 py-4 sm:border-s sm:border-t-0 sm:px-7 dark:border-neutral-200"
                                        >
                                            <p
                                                class="text-xs font-semibold text-neutral-400 dark:text-neutral-500"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.available_rooms',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1.5 font-semibold tabular-nums"
                                            >
                                                {{ metrics.availableRooms }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                                >
                                    <div
                                        class="border-b border-neutral-200 px-5 py-5 sm:px-7 dark:border-neutral-800"
                                    >
                                        <h3
                                            class="font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{
                                                t(
                                                    'hotels.management.forecast.title',
                                                )
                                            }}
                                        </h3>
                                        <p
                                            class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            {{
                                                t(
                                                    'hotels.management.forecast.description',
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div class="px-3 py-5 sm:px-6">
                                        <OccupancyForecastChart
                                            :points="props.dashboard.forecast"
                                        />
                                    </div>
                                </div>
                            </div>

                            <aside
                                class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                            >
                                <div
                                    class="border-b border-neutral-200 px-5 py-5 sm:px-6 dark:border-neutral-800"
                                >
                                    <h3
                                        class="font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{
                                            t(
                                                'hotels.management.operations.title',
                                            )
                                        }}
                                    </h3>
                                    <p
                                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{
                                            t(
                                                'hotels.management.operations.description',
                                            )
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="divide-y divide-neutral-200 dark:divide-neutral-800"
                                >
                                    <div
                                        class="flex items-center gap-4 px-5 py-5 sm:px-6"
                                    >
                                        <span
                                            class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                            ><CalendarDaysIcon class="h-5 w-5"
                                        /></span>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-sm text-neutral-600 dark:text-neutral-400"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.arrivals_today',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                                            >
                                                {{ metrics.arrivalsToday }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-4 px-5 py-5 sm:px-6"
                                    >
                                        <span
                                            class="bg-secondary-50 text-secondary-800 dark:bg-secondary-900/30 dark:text-secondary-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                            ><BuildingOffice2Icon
                                                class="h-5 w-5"
                                        /></span>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-sm text-neutral-600 dark:text-neutral-400"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.departures_today',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                                            >
                                                {{ metrics.departuresToday }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-4 px-5 py-5 sm:px-6"
                                    >
                                        <span
                                            class="bg-success-50 text-success-700 dark:bg-success-900/30 dark:text-success-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                            ><UserGroupIcon class="h-5 w-5"
                                        /></span>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-sm text-neutral-600 dark:text-neutral-400"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.guests_in_house',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                                            >
                                                {{ metrics.guestsInHouse }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-4 px-5 py-5 sm:px-6"
                                    >
                                        <span
                                            class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                            ><RectangleGroupIcon
                                                class="h-5 w-5"
                                        /></span>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-sm text-neutral-600 dark:text-neutral-400"
                                            >
                                                {{
                                                    t(
                                                        'hotels.management.metrics.dirty_rooms',
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-semibold text-neutral-950 tabular-nums dark:text-white"
                                            >
                                                {{ metrics.dirtyRooms }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="px-5 py-5 sm:px-6">
                                        <div
                                            class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400"
                                        >
                                            <BanknotesIcon class="h-5 w-5" />
                                            {{
                                                t(
                                                    'hotels.management.metrics.outstanding_balance',
                                                )
                                            }}
                                        </div>
                                        <p
                                            class="mt-2 text-xl font-semibold tracking-[-0.02em] text-neutral-950 tabular-nums dark:text-white"
                                        >
                                            {{
                                                money(
                                                    metrics.outstandingBalanceMinor,
                                                    true,
                                                )
                                            }}
                                        </p>
                                        <p
                                            class="mt-2 text-xs leading-5 text-neutral-500 dark:text-neutral-500"
                                        >
                                            {{
                                                t(
                                                    'hotels.management.metrics.posted_balance_note',
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
