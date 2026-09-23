<script setup>
import ActionLink from '@/Components/ActionLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import {
    ArrowRightIcon,
    BookmarkSquareIcon,
    MagnifyingGlassIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    hotel: Object,
    reservations: Object,
    filters: Object,
    statuses: Array,
    currency: String,
});
const { t, locale } = useI18n();
const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
let filterTimer;

function applyFilters() {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => {
        router.get(
            route('hotels.reservations.index', props.hotel.id),
            {
                search: search.value || undefined,
                status: status.value || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 250);
}
function nights(reservation) {
    return Math.round(
        (new Date(`${reservation.planned_check_out_on}T00:00:00`) -
            new Date(`${reservation.planned_check_in_on}T00:00:00`)) /
            86400000,
    );
}
function total(reservation) {
    return reservation.reserved_rooms.reduce(
        (sum, room) => sum + Number(room.nightly_rate) * nights(reservation),
        0,
    );
}
function formatMoney(value) {
    return new Intl.NumberFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
        style: 'currency',
        currency: props.currency,
    }).format(value);
}
function statusClass(value) {
    if (value === 'confirmed')
        return 'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-300';
    if (value === 'cancelled' || value === 'no_show')
        return 'bg-danger-50 text-danger-800 dark:bg-danger-900/25 dark:text-danger-200';
    return 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300';
}

watch([search, status], applyFilters);
</script>

<template>
    <Head :title="t('reservations.title')" />
    <DefaultLayout :hotel="hotel">
        <div class="w-full px-4 py-8 sm:px-6 sm:py-10 lg:px-8 2xl:px-10">
            <div class="grid gap-7">
                <PageHeader
                    :title="t('reservations.title')"
                    :description="
                        t('reservations.pages.index.description', {
                            hotel: hotel.business_name,
                        })
                    "
                >
                    <template #actions>
                        <ActionLink
                            :href="
                                route('hotels.reservations.create', hotel.id)
                            "
                        >
                            <PlusIcon class="h-4 w-4" />{{
                                t('reservations.actions.create')
                            }}
                        </ActionLink>
                    </template>
                </PageHeader>

                <section
                    class="grid gap-4 rounded-2xl bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_14rem] dark:bg-neutral-900"
                >
                    <div class="relative">
                        <MagnifyingGlassIcon
                            class="absolute inset-s-3 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-500"
                        />
                        <TextInput
                            v-model="search"
                            class="w-full ps-10"
                            :placeholder="t('reservations.pages.index.search')"
                        />
                    </div>
                    <select
                        v-model="status"
                        class="focus:border-primary-500 focus:ring-primary-500/20 min-h-11 rounded-lg border border-neutral-300 bg-white px-3.5 text-sm text-neutral-900 shadow-sm focus:ring-2 focus:outline-hidden dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                    >
                        <option value="">
                            {{ t('reservations.pages.index.all_statuses') }}
                        </option>
                        <option
                            v-for="item in statuses"
                            :key="item"
                            :value="item"
                        >
                            {{ t(`reservations.statuses.${item}`) }}
                        </option>
                    </select>
                </section>

                <section
                    v-if="reservations.data.length"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-neutral-900"
                >
                    <ul
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        <li
                            v-for="reservation in reservations.data"
                            :key="reservation.id"
                            class="grid gap-5 px-5 py-5 sm:px-7 lg:grid-cols-[10rem_minmax(14rem,1fr)_minmax(12rem,0.7fr)_auto] lg:items-center"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold text-neutral-500"
                                >
                                    {{ t('reservations.fields.check_in') }}
                                </p>
                                <p
                                    class="mt-1 text-lg font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ reservation.planned_check_in_on }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-neutral-600 tabular-nums dark:text-neutral-400"
                                >
                                    {{ nights(reservation) }}
                                    {{
                                        t(
                                            'reservations.fields.nights',
                                        ).toLowerCase()
                                    }}
                                </p>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <Link
                                        :href="
                                            route('hotels.reservations.show', [
                                                hotel.id,
                                                reservation.id,
                                            ])
                                        "
                                        class="focus-visible:ring-primary-500 hover:text-primary-700 dark:hover:text-primary-300 rounded font-semibold text-neutral-950 focus-visible:ring-2 focus-visible:outline-hidden dark:text-white"
                                    >
                                        {{
                                            reservation.responsible_guest
                                                .first_name
                                        }}
                                        {{
                                            reservation.responsible_guest
                                                .last_name
                                        }}
                                    </Link>
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="statusClass(reservation.status)"
                                        >{{
                                            t(
                                                `reservations.statuses.${reservation.status}`,
                                            )
                                        }}</span
                                    >
                                </div>
                                <p
                                    class="mt-2 text-sm text-neutral-600 dark:text-neutral-400"
                                >
                                    {{
                                        reservation.reserved_rooms
                                            .map((item) => item.room.number)
                                            .join(', ')
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-semibold text-neutral-500"
                                >
                                    {{ t('reservations.fields.quote') }}
                                </p>
                                <p
                                    class="mt-1 font-semibold text-neutral-950 tabular-nums dark:text-white"
                                >
                                    {{ formatMoney(total(reservation)) }}
                                </p>
                            </div>
                            <ActionLink
                                :href="
                                    route('hotels.reservations.show', [
                                        hotel.id,
                                        reservation.id,
                                    ])
                                "
                                variant="secondary"
                                size="sm"
                                >{{ t('reservations.actions.view')
                                }}<ArrowRightIcon class="h-4 w-4"
                            /></ActionLink>
                        </li>
                    </ul>
                    <Pagination :pagination="reservations" />
                </section>

                <section
                    v-else
                    class="flex min-h-80 flex-col items-center justify-center rounded-2xl bg-white px-6 py-12 text-center shadow-sm dark:bg-neutral-900"
                >
                    <span
                        class="bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl"
                        ><BookmarkSquareIcon class="h-7 w-7"
                    /></span>
                    <h2
                        class="mt-5 text-lg font-semibold text-neutral-950 dark:text-white"
                    >
                        {{ t('reservations.pages.index.empty_title') }}
                    </h2>
                    <p
                        class="mt-2 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ t('reservations.pages.index.empty') }}
                    </p>
                    <ActionLink
                        :href="route('hotels.reservations.create', hotel.id)"
                        class="mt-6"
                        ><PlusIcon class="h-4 w-4" />{{
                            t('reservations.actions.create')
                        }}</ActionLink
                    >
                </section>
            </div>
        </div>
    </DefaultLayout>
</template>
